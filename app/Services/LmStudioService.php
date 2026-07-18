<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LmStudioService
{
    protected string $apiUrl;
    protected string $model;
    protected string $embeddingModel;

    public function __construct()
    {
        $this->apiUrl = trim(config('services.lmstudio.url', 'http://localhost:1234/v1'));
        $this->model  = trim(config('services.lmstudio.model', 'gemma-4-e4b'));
        $this->embeddingModel = trim(config('services.lmstudio.embedding_model', 'bge-m3'));
    }

    public function chat(array $history, string $ragContext = ''): array
    {
        $systemPrompt = $this->buildSystemPrompt();
        
        if (!empty($ragContext)) {
            $systemPrompt .= "\n\nINFORMASI TAMBAHAN (Konteks RAG):\n" . $ragContext;
        }

        // Convert Gemini history format to OpenAI format
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($history as $msg) {
            $role = $msg['role'] === 'model' ? 'assistant' : 'user';
            $content = $msg['parts'][0]['text'] ?? '';
            $messages[] = ['role' => $role, 'content' => $content];
        }

        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => 0.7,
            'max_tokens'  => 4096,
        ];

        try {
            if (function_exists('set_time_limit')) {
                @set_time_limit(180);
            }

            $response = Http::timeout(120)
                ->withOptions(['connect_timeout' => 10])
                ->post("{$this->apiUrl}/chat/completions", $payload);

            if ($response->failed()) {
                Log::error('LM Studio API error', ['status' => $response->status(), 'body' => $response->body()]);
                return ['message' => 'Maaf, terjadi kesalahan saat menghubungi AI Lokal. Pastikan LM Studio sedang berjalan.'];
            }

            $text = $response->json('choices.0.message.content', '');
            Log::info('LM Studio raw response', ['text' => $text]);
            return $this->parseResponse($text);

        } catch (\Exception $e) {
            Log::error('LM Studio exception', ['error' => $e->getMessage()]);
            return ['message' => 'Terjadi gangguan koneksi ke LM Studio. Pastikan server lokal sudah aktif di ' . $this->apiUrl];
        }
    }

    public function embed(string $text): ?array
    {
        $payload = [
            'model' => $this->embeddingModel,
            'input' => $text
        ];

        try {
            $response = Http::timeout(60)
                ->post("{$this->apiUrl}/embeddings", $payload);

            if ($response->successful()) {
                return $response->json('data.0.embedding');
            }
            
            Log::error('LM Studio Embeddings error', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('LM Studio Embeddings exception', ['error' => $e->getMessage()]);
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
                Log::warning('LM Studio JSON parse error', ['error' => $e->getMessage()]);
                return ['message' => 'Maaf, itinerary berhasil dibuat namun formatnya terpotong atau tidak valid. Silakan coba "Mulai Ulang" atau kirim ulang permintaanmu.'];
            }
        }

        return ['message' => trim($text)];
    }

    protected function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah NusantaraAI, asisten perencanaan wisata Indonesia yang ramah dan berpengetahuan luas.

TUGASMU:
1. Bantu pengguna merencanakan perjalanan wisata di Indonesia
2. Ketika pengguna memberikan informasi lengkap (destinasi + durasi + budget), buat itinerary dalam format JSON
3. Untuk pertanyaan umum, jawab dengan bahasa Indonesia yang hangat dan informatif

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
