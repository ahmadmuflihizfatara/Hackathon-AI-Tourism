<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiApiService
{
    protected string $apiKey;
    protected string $model;
    protected string $embeddingModel;

    public function __construct()
    {
        $this->apiKey = trim(config('services.gemini.api_key', ''));
        $this->model  = trim(config('services.gemini.model', 'gemini-1.5-flash'));
        $this->embeddingModel = trim(config('services.gemini.embedding_model', 'gemini-embedding-2'));
    }

    public function chat(array $history, string $ragContext = ''): array
    {
        if (empty($this->apiKey)) {
            return ['message' => 'API Key Gemini belum diatur di file .env (GEMINI_API_KEY).'];
        }

        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

        $systemPrompt = $this->buildSystemPrompt();
        
        if (!empty($ragContext)) {
            $systemPrompt .= "\n\nINFORMASI TAMBAHAN (Konteks RAG):\n" . $ragContext;
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => $history,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 8192,
            ]
        ];

        try {
            $response = Http::timeout(120) // allow longer response time for Gemini
                ->retry(2, 1000, throw: false)
                ->withoutVerifying() // Bypass SSL error cURL 60 di XAMPP Windows
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", $payload);

            if ($response->failed()) {
                Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
                return ['message' => 'Maaf, terjadi kesalahan saat menghubungi server Google Gemini.'];
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');
            Log::info('Gemini API raw response', ['text' => substr($text, 0, 500) . '...']);
            return $this->parseResponse($text);

        } catch (\Exception $e) {
            Log::error('Gemini API exception', ['error' => $e->getMessage()]);
            return ['message' => 'Terjadi gangguan koneksi ke Google Gemini. Silakan coba lagi.'];
        }
    }

    public function embed(string $text): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is missing for embeddings.');
            return null;
        }

        $payload = [
            'model' => 'models/' . $this->embeddingModel,
            'content' => [
                'parts' => [['text' => $text]]
            ]
        ];

        try {
            $response = Http::timeout(30)
                ->withoutVerifying() // Bypass SSL error cURL 60 di XAMPP Windows
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->embeddingModel}:embedContent?key={$this->apiKey}", $payload);

            if ($response->successful()) {
                return $response->json('embedding.values');
            }
            
            Log::error('Gemini API Embeddings error', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Gemini API Embeddings exception', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function parseResponse(string $text): array
    {
        $json = null;

        if (($start = strpos(strtolower($text), '```json')) !== false) {
            $start += 7;
            if (($end = strpos($text, '```', $start)) !== false) {
                $json = substr($text, $start, $end - $start);
            } else {
                $json = substr($text, $start);
            }
        } elseif (($start = strpos($text, '{')) !== false && strpos($text, '"destination"') !== false) {
            $end = strrpos($text, '}');
            if ($end !== false && $end > $start) {
                $json = substr($text, $start, $end - $start + 1);
            } else {
                $json = substr($text, $start);
            }
        }

        if ($json) {
            try {
                $json = trim($json);
                $json = preg_replace('/,\s*([\]}])/m', '$1', $json);
                $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

                if (isset($data['destination'])) {
                    return [
                        'itinerary' => $data,
                        'message'   => "Itinerary untuk **{$data['destination']}** sudah siap! 🎉 Cek panel kanan untuk detail lengkapnya. Ada yang ingin diubah atau ditambah?",
                    ];
                }
            } catch (\JsonException $e) {
                Log::warning('Gemini API JSON parse error', ['error' => $e->getMessage()]);
                return ['message' => 'Maaf, itinerary berhasil dibuat namun formatnya terpotong atau tidak valid. Silakan coba "Mulai Ulang" atau kirim ulang permintaanmu.'];
            }
        }

        return ['message' => trim($text)];
    }

    protected function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah Toba Itinerary, asisten perencanaan wisata di wilayah Danau Toba yang ramah dan berpengetahuan luas.

TUGASMU:
1. Bantu pengguna merencanakan perjalanan wisata di Indonesia
2. Ketika pengguna memberikan informasi lengkap (destinasi + durasi + budget), buat itinerary dalam format JSON.
3. ATURAN MUTLAK: Untuk nama tempat wisata, restoran, dan hotel (`location`), kamu HANYA BOLEH menggunakan nama tempat yang secara eksplisit ada di bagian INFORMASI TAMBAHAN (Konteks RAG). DILARANG KERAS mengarang, berimajinasi, atau mengambil nama tempat dari luar dataset.
4. Jika budget atau waktu masih sisa tetapi referensi tempat di dataset habis, lebih baik biarkan kosong/beri waktu istirahat daripada mengarang tempat palsu.
5. Untuk pertanyaan umum, jawab dengan bahasa Indonesia yang hangat dan informatif.

FORMAT RESPONS ITINERARY:
Ketika membuat itinerary, HARUS dalam format JSON ini:

```json
{
  "destination": "Nama Kota/Daerah",
  "province": "Nama Provinsi",
  "days": 5,
  "total_budget": "Rp 3.000.000",
  "total_places": 12,
  "schedule": [
    {
      "day": 1,
      "title": "Tema Hari Ini",
      "activities": [
        {
          "time": "08.00",
          "action": "Kata kerja singkat aktivitas",
          "location": "Nama lokasi/tempat spesifik",
          "category": "alam|pantai|museum|kuliner|hotel|belanja|transport|budaya",
          "description": "Deskripsi singkat 1-2 kalimat",
          "ticket": "Rp 50.000 (opsional, null jika gratis)",
          "harga_min": 50000,
          "harga_max": 150000,
          "wna_price": "USD 25 (opsional, isi HANYA jika ada tarif khusus wisatawan asing, selain itu null)"
        }
      ]
    }
  ],
  "budget": [
    {
      "category": "akomodasi",
      "label": "Hotel/Penginapan",
      "amount": 1200000,
      "per": "per malam × 4",
      "note": "Hotel bintang 2-3 di pusat kota"
    }
  ],
  "tips": [
    {
      "title": "Transportasi",
      "content": "Sewa motor di Bali sekitar Rp 70-100 ribu per hari, lebih hemat dari taksi."
    }
  ]
}
```

ATURAN PENTING:
- SANGAT PENTING: Format JSON harus valid. JANGAN gunakan trailing comma. PASTIKAN semua kurung kurawal } dan kurung siku ] tertutup sempurna di akhir JSON.
PROMPT;
    }
}
