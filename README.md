# 🌴 NusantaraAI — Tourism Itinerary Planner

Aplikasi web perencanaan wisata Indonesia berbasis AI menggunakan **Laravel 11**, **Blade**, **Tailwind CSS**, dan **Google Gemini AI**.

---

## 🎨 Desain & Teknologi

| Elemen | Nilai |
|---|---|
| **Framework** | Laravel 11 + Blade |
| **CSS** | Tailwind CSS v3 + Vite |
| **AI** | Google Gemini 1.5 Flash (gratis) |
| **Icons** | Google Material Icons Round |
| **Font Judul** | Poppins |
| **Font Body** | Open Sans |
| **Warna Utama** | Terracotta `#C1602B` |
| **Background** | Warm Sand `#F5EFE6` |
| **Aksen** | Emerald Green `#3A7D5C` |

---

## 📁 Struktur Proyek

```
nusantaraai/
├── app/
│   ├── Http/Controllers/
│   │   ├── PageController.php      ← Landing & Dashboard pages
│   │   └── GeminiController.php    ← API endpoint untuk Gemini
│   └── Services/
│       └── GeminiService.php       ← Logika komunikasi ke Gemini API
├── resources/
│   ├── css/
│   │   └── app.css                 ← Tailwind + custom utilities
│   ├── js/
│   │   └── app.js                  ← Entry point Vite
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php       ← Layout utama (fonts, icons, meta)
│       ├── pages/
│       │   ├── landing.blade.php   ← Halaman beranda dengan chatbot
│       │   └── dashboard.blade.php ← Dashboard 2 kolom (chat + itinerary)
│       ├── components/
│       │   ├── stat-card.blade.php
│       │   ├── chat-bubble.blade.php
│       │   ├── activity-item.blade.php
│       │   ├── budget-row.blade.php
│       │   └── tip-card.blade.php
│       └── errors/
│           ├── 404.blade.php
│           └── 500.blade.php
├── routes/
│   ├── web.php                     ← Route halaman (GET /, GET /dashboard)
│   └── api.php                     ← Route API (POST /api/gemini)
├── config/
│   └── services.php                ← Konfigurasi Gemini API key
├── tailwind.config.js              ← Konfigurasi warna & font custom
├── vite.config.js
├── postcss.config.js
├── package.json
└── .env.example
```

---

## 🚀 Cara Instalasi (Lokal)

### 1. Buat Proyek Laravel Baru

```bash
composer create-project laravel/laravel nusantaraai
cd nusantaraai
```

### 2. Copy Semua File dari Repo Ini

Salin semua file sesuai struktur di atas ke dalam folder proyek Laravel kamu.

### 3. Install Dependensi PHP

```bash
composer require guzzlehttp/guzzle
```

### 4. Install Dependensi Node.js

```bash
npm install
```

### 5. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

---

## 🔑 Cara Mendapatkan & Menggunakan Gemini API (GRATIS)

### Langkah 1 — Daftar di Google AI Studio

1. Buka **https://aistudio.google.com/**
2. Login dengan akun Google kamu
3. Klik **"Get API Key"** di menu kiri atas
4. Klik **"Create API Key"**
5. Pilih project Google Cloud (atau buat baru — gratis)
6. **Copy API Key** yang muncul (format: `AIzaSy...`)

> ✅ **Gemini 1.5 Flash gratis** dengan limit:
> - 15 request per menit
> - 1.000.000 token per menit  
> - 1.500 request per hari
> Cukup untuk development dan demo!

### Langkah 2 — Tambahkan ke file `.env`

Buka file `.env` di root project, cari dan isi:

```env
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
GEMINI_MODEL=gemini-1.5-flash
```

### Langkah 3 — Verifikasi Konfigurasi

Di `config/services.php` sudah terdapat:

```php
'gemini' => [
    'key'   => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
],
```

### Langkah 4 — Cara Kerja Alur API

```
Browser (dashboard.blade.php)
    │  POST /api/gemini  { history: [...] }
    ▼
GeminiController.php
    │  Validasi input
    ▼
GeminiService.php
    │  Tambah system prompt
    │  POST ke Gemini API
    ▼
Google Gemini 1.5 Flash
    │  Return teks / JSON itinerary
    ▼
GeminiService::parseResponse()
    │  Detect JSON itinerary atau plain chat
    ▼
Browser
    │  Render chat bubble ATAU render itinerary panel
```

---

## ▶️ Menjalankan Aplikasi Lokal

Buka **2 terminal** secara bersamaan:

**Terminal 1 — Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 — Vite (CSS/JS hot reload):**
```bash
npm run dev
```

Lalu buka browser: **http://localhost:8000**

---

## 🗺️ Alur Pengguna

```
[Landing Page /]
   Pengguna ketik destinasi di chatbot
        ↓
   Redirect ke [Dashboard /dashboard?q=...]
        ↓
   ┌──────────────────┬─────────────────────────────┐
   │  KOLOM KIRI      │  KOLOM KANAN                │
   │  Chat AI         │  Hasil Itinerary             │
   │  ─────────────── │  ──────────────────────────  │
   │  • Input teks    │  • Header destinasi          │
   │  • Bubble chat   │  • Tab: Jadwal / Budget / Tips│
   │  • Typing anim.  │  • Day cards per hari        │
   │  • Follow-up Q   │  • Budget breakdown          │
   └──────────────────┴─────────────────────────────┘
```

---

## 💬 Contoh Prompt yang Bisa Digunakan

- `"Mau ke Bali 5 hari, budget Rp 3 juta, suka pantai dan kuliner"`
- `"Rencanakan wisata Yogyakarta 3 hari untuk keluarga dengan 2 anak"`
- `"Itinerary Raja Ampat 7 hari budget Rp 10 juta, fokus snorkeling"`
- `"Wisata sejarah Solo 2 hari budget hemat Rp 500 ribu"`

---

## 🛠️ Troubleshooting

### ❌ Error: "API Key tidak valid"
→ Pastikan `.env` sudah diisi `GEMINI_API_KEY` dengan benar  
→ Jalankan `php artisan config:clear`

### ❌ CSS tidak muncul / tampilan rusak
→ Pastikan `npm run dev` sedang berjalan di terminal terpisah  
→ Atau build dulu: `npm run build`

### ❌ Error 419 (CSRF)
→ Pastikan `<meta name="csrf-token">` ada di layout  
→ Jalankan `php artisan key:generate` jika `APP_KEY` kosong

### ❌ Error "Route not found"
→ Jalankan `php artisan route:clear && php artisan route:cache`

### ❌ Gemini tidak merespons / timeout
→ Cek koneksi internet  
→ Cek limit harian di https://aistudio.google.com/  
→ Model `gemini-1.5-flash` lebih cepat dari `gemini-1.5-pro`

---

## 📦 Perintah Artisan Berguna

```bash
# Bersihkan cache
php artisan optimize:clear

# Lihat semua routes
php artisan route:list

# Cache config untuk production
php artisan config:cache

# Cache routes
php artisan route:cache
```

---

## 🔮 Fitur yang Bisa Dikembangkan Selanjutnya

- [ ] Export itinerary ke PDF (menggunakan `barryvdh/laravel-dompdf`)
- [ ] Peta interaktif dengan Leaflet.js + OpenStreetMap
- [ ] Foto tempat wisata dari Unsplash API (gratis)
- [ ] Simpan itinerary ke database (login pengguna)
- [ ] Share itinerary via link unik
- [ ] Mode offline / PWA
- [ ] Multi-bahasa (Indonesia + Inggris)

---

## 📄 Lisensi

MIT License — bebas digunakan untuk keperluan edukasi dan hackathon.

---

Dibuat dengan ❤️ untuk **UI/UX Hackathon AI Tourism** 🌴
