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

    public function chat(array $history): array
    {
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
          if (function_exists('set_time_limit')) {
            @set_time_limit(120);
          }

          $response = Http::retry(3, function (int $attempt, \Exception $exception) {
              return $attempt * 2000;
          }, function (\Exception $exception) {
              if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                  return true;
              }
              if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                  return in_array($exception->response->status(), [429, 500, 502, 503, 504]);
              }
              return false;
          }, false)
            ->timeout(60)
            ->withOptions(['connect_timeout' => 10])
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
          Log::info('Gemini raw response', ['text' => $text]);
          return $this->parseResponse($text);

        } catch (\Exception $e) {
          Log::error('Gemini exception', ['error' => $e->getMessage()]);
          return ['message' => 'Terjadi gangguan koneksi. Silakan coba lagi dalam beberapa saat.'];
        }
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
                Log::warning('Gemini JSON parse error', ['error' => $e->getMessage()]);
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
- SANGAT PENTING: Format JSON harus valid. JANGAN gunakan trailing comma. PASTIKAN semua kurung kurawal } dan kurung siku ] tertutup sempurna di akhir JSON.

ATURAN FIELD harga_min DAN harga_max (WAJIB UNTUK SETIAP AKTIVITAS):
Field "harga_min" dan "harga_max" WAJIB diisi untuk SETIAP aktivitas tanpa terkecuali.
Kedua field berisi angka integer dalam satuan Rupiah (tanpa simbol, tanpa titik, tanpa koma).
Gunakan panduan estimasi berikut dan sesuaikan dengan kota tujuan:

- transport antar kota (bus/travel/kereta): harga_min: 50000, harga_max: 250000
- transport dalam kota (ojek/taksi/angkot): harga_min: 10000, harga_max: 60000
- transport sewa motor: harga_min: 70000, harga_max: 120000
- transport sewa mobil: harga_min: 300000, harga_max: 600000
- hotel budget: harga_min: 100000, harga_max: 350000
- hotel menengah: harga_min: 350000, harga_max: 800000
- hotel bintang: harga_min: 800000, harga_max: 2000000
- kuliner warung/kaki lima: harga_min: 10000, harga_max: 35000
- kuliner restoran lokal: harga_min: 35000, harga_max: 100000
- kuliner restoran menengah: harga_min: 80000, harga_max: 200000
- tiket wisata alam/pantai umum: harga_min: 5000, harga_max: 30000
- tiket wisata alam premium: harga_min: 25000, harga_max: 75000
- tiket museum/budaya: harga_min: 5000, harga_max: 50000
- tiket wisata ikonik (Borobudur, Prambanan, Komodo, dll): harga_min: 50000, harga_max: 750000
- belanja oleh-oleh/pasar: harga_min: 50000, harga_max: 500000
- aktivitas gratis (taman, alun-alun, masjid, pantai umum): harga_min: 0, harga_max: 0

ATURAN FIELD wna_price (OPSIONAL):
Field "wna_price" HANYA diisi untuk destinasi yang memiliki tarif berbeda untuk wisatawan asing (WNA).
Contoh destinasi yang WAJIB diisi wna_price: Borobudur, Prambanan, Taman Nasional Komodo,
Bali Safari & Marine Park, Taman Nasional Bromo, dan destinasi serupa dengan dual pricing.
Format nilai: string seperti "USD 25" atau "Rp 750.000".
Untuk semua destinasi lain yang tidak ada tarif WNA khusus → isi null.

Contoh pengisian wna_price yang BENAR:
- Tiket Candi Borobudur → wna_price: "USD 25"
- Tiket Prambanan → wna_price: "USD 25"
- Tiket Taman Nasional Komodo → wna_price: "USD 10"
- Makan di warung, ojek, hotel → wna_price: null

ATURAN FIELD action DAN location (WAJIB DIIKUTI):
Field "action" berisi kata kerja/aktivitas singkat (1-3 kata), dan "location" berisi nama tempat spesifiknya.
Keduanya WAJIB ada di setiap aktivitas. JANGAN gunakan field "place" lagi.

Contoh yang BENAR:
- "action": "Tiba di",        "location": "Bandara Sultan Mahmud Badaruddin II"
- "action": "Check-in",       "location": "Hotel Aryaduta Palembang"
- "action": "Kunjungi",       "location": "Jembatan Ampera"
- "action": "Makan Siang",    "location": "RM Pindang Musi Rawas"
- "action": "Jelajahi",       "location": "Kawasan Benteng Kuto Besak"
- "action": "Sewa Motor",     "location": "Rental Motor Seminyak"
- "action": "Nikmati Sunset", "location": "Pantai Tanah Lot"
- "action": "Belanja",        "location": "Pasar Seni Sukawati"
- "action": "Check-out",      "location": "Hotel / Penginapan"
- "action": "Makan Malam",    "location": "Warung Sate Pak Budi"

BAHASA: Selalu gunakan Bahasa Indonesia yang ramah, hangat, dan antusias.
PROMPT;
    }
}