<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = trim(config('services.gemini.key', ''));
        $this->model  = trim(config('services.gemini.model', 'gemini-2.5-flash'));
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Send a conversation history to Gemini and get a structured response.
     * Returns either a chat message OR a full itinerary JSON.
     */
    public function chat(array $history): array
    {
        // Prepend system instruction as first user turn
        $systemPrompt = $this->buildSystemPrompt();

        $contents = [
            ['role' => 'user',  'parts' => [['text' => $systemPrompt]]],
            ['role' => 'model', 'parts' => [['text' => 'Siap! Saya NusantaraAI, asisten perencanaan wisata Indonesia. Saya akan membantu membuat itinerary lengkap sesuai preferensimu.']]],
            ...$history,
        ];

        $payload = [
            'contents'          => $contents,
            'generationConfig'  => [
                'temperature'     => 0.7,
                'topK'            => 40,
                'topP'            => 0.95,
                'maxOutputTokens' => 8192,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ],
        ];

        try {
          // Prevent PHP process from timing out too quickly for longer API calls.
          if (function_exists('set_time_limit')) {
            @set_time_limit(60);
          }

          // Use a reasonable timeout and a short connect timeout to fail fast on network issues.
          $response = Http::timeout(20)
            ->withOptions(['connect_timeout' => 5])
            ->withoutVerifying()
            ->withQueryParameters(['key' => $this->apiKey])
            ->post($this->apiUrl, $payload);

          $status = $response->status();

          if ($status === 429) {
            Log::warning('Gemini rate limited', ['status' => $status, 'body' => $response->body()]);
            return ['message' => 'Layanan AI sedang mencapai batas kuota. Silakan coba lagi nanti.'];
          }

          if ($status === 503) {
            Log::warning('Gemini unavailable', ['status' => $status, 'body' => $response->body()]);
            return ['message' => 'Layanan AI sedang sibuk. Silakan coba lagi sebentar.'];
          }

          if ($response->failed()) {
            Log::error('Gemini API error', ['status' => $status, 'body' => $response->body()]);
            return ['message' => 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi.'];
          }

          $text = $response->json('candidates.0.content.parts.0.text', '');
          return $this->parseResponse($text);

        } catch (\Exception $e) {
          Log::error('Gemini exception', ['error' => $e->getMessage()]);
          return ['message' => 'Terjadi gangguan koneksi. Silakan coba lagi dalam beberapa saat.'];
        }
    }

    /**
     * Parse Gemini's text response.
     * If it contains JSON, extract and return as structured itinerary.
     * Otherwise return as plain chat message.
     */
    protected function parseResponse(string $text): array
    {
        // Try to extract JSON block from response
        if (preg_match('/```json\s*([\s\S]*?)\s*```/', $text, $matches)) {
            $json = $matches[1];
        } elseif (preg_match('/\{[\s\S]*"destination"[\s\S]*\}/', $text, $matches)) {
            $json = $matches[0];
        } else {
            // Plain chat response
            return ['message' => trim($text)];
        }

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            if (isset($data['destination'])) {
                return [
                    'itinerary' => $data,
                    'message'   => "Itinerary untuk **{$data['destination']}** sudah siap! 🎉 Cek panel kanan untuk detail lengkapnya. Ada yang ingin diubah atau ditambah?",
                ];
            }
        } catch (\JsonException $e) {
            Log::warning('Gemini JSON parse error', ['json' => $json]);
        }

        return ['message' => trim($text)];
    }

    /**
     * Build the system prompt that instructs Gemini on its role and output format.
     */
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
          "place": "Nama Tempat",
          "category": "alam|pantai|museum|kuliner|hotel|belanja|transport|budaya",
          "description": "Deskripsi singkat 1-2 kalimat",
          "ticket": "Rp 50.000 (opsional, null jika gratis)"
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
    },
    {
      "category": "makan",
      "label": "Makan & Minum",
      "amount": 600000,
      "per": "Rp 120.000/hari × 5"
    },
    {
      "category": "transport",
      "label": "Transportasi Lokal",
      "amount": 500000,
      "per": "estimasi total",
      "note": "Ojek, angkutan, dll"
    },
    {
      "category": "tiket",
      "label": "Tiket Masuk Wisata",
      "amount": 350000,
      "per": "estimasi total"
    },
    {
      "category": "lainnya",
      "label": "Oleh-oleh & Lainnya",
      "amount": 350000,
      "per": "estimasi total"
    }
  ],
  "tips": [
    {
      "title": "Transportasi",
      "content": "Sewa motor di Bali sekitar Rp 70-100 ribu per hari, lebih hemat dari taksi."
    },
    {
      "title": "Waktu Terbaik",
      "content": "Kunjungi Tanah Lot saat sunset sekitar pukul 17.30-18.30 untuk foto terbaik."
    },
    {
      "title": "Kuliner Wajib",
      "content": "Jangan lewatkan Babi Guling Ibu Oka dan Bebek Bengil yang legendaris."
    }
  ]
}
```

ATURAN PENTING:
- Selalu rekomendasikan tempat wisata yang nyata dan populer
- Sesuaikan jumlah aktivitas dengan durasi (max 4-5 lokasi per hari)
- Pastikan total budget sesuai dengan yang diminta pengguna
- Gunakan waktu yang realistis (pertimbangkan jarak dan waktu perjalanan)
- Kategori wisata: alam, pantai, museum, kuliner, budaya, belanja, transport, hotel
- Tambahkan tips lokal yang praktis dan bermanfaat
- Jika informasi tidak lengkap, tanyakan: provinsi/kota tujuan, berapa hari, budget total

BAHASA: Selalu gunakan Bahasa Indonesia yang ramah, hangat, dan antusias.
PROMPT;
    }
}
