# 🌴 NusantaraAI — Tourism Itinerary Planner (Gemini API / RAG Version)

Aplikasi web perencanaan wisata Indonesia berbasis AI menggunakan **Laravel 11**, **Blade**, **Tailwind CSS**, dan **Gemini API**.

Aplikasi ini memanfaatkan teknologi *Retrieval-Augmented Generation* (RAG) untuk memberikan data tempat wisata yang lebih akurat sesuai dataset (XLSX).

---

## 🎨 Desain & Teknologi

| Elemen | Nilai |
|---|---|
| **Framework** | Laravel 11 + Blade |
| **CSS** | Tailwind CSS v3 + Vite |
| **AI Backend** | Gemini API |
| **LLM Model** | Gemini Pro |
| **Database RAG** | Chroma DB (Vector Database) |
| **Icons** | Google Material Icons Round |
| **Warna Utama** | Terracotta `#C1602B` |

---

## 📁 Struktur Proyek Utama Terkait RAG

```
nusantaraai/
├── app/
│   ├── Console/Commands/
│   │   └── IngestDatasetCommand.php ← Command untuk import dataset XLSX
│   ├── Models/
│   │   └── KnowledgeBase.php        ← Model (bila masih ada untuk fallback)
│   ├── Services/
│   │   ├── GeminiService.php        ← Logika komunikasi ke Gemini API
│   │   └── ChromaDbService.php      ← Logika integrasi ke Chroma DB
├── database/
│   └── migrations/
│       └── ...create_knowledge_bases_table.php ← Skema tabel vector RAG (lama)
├── storage/
│   ├── app/Storage/
│   │   └── Dataset_Toba_Normalized.xlsx ← Dataset acuan RAG
│   └── chromadb/                    ← Folder penyimpan database vektor Chroma
```

---

## 🚀 Langkah-langkah Instalasi & Menjalankan Aplikasi

Ikuti panduan berikut secara berurutan agar aplikasi berjalan dengan lancar.

### Tahap 1: Persiapan Vector Database (Chroma DB)
Chroma DB digunakan untuk menyimpan vektor embedding destinasi wisata.
1. Pastikan kamu sudah menginstal **Python**.
2. Buka terminal baru dan install library Chroma DB:
   ```bash
   pip install chromadb
   ```
3. Jalankan server lokal Chroma DB di port `8002` agar tidak bentrok dengan server Laravel:
   ```bash
   chroma run --path ./storage/chromadb --port 8002
   ```
   *(Biarkan terminal ini berjalan di background)*

### Tahap 2: Persiapan Aplikasi Laravel
1. Buka terminal baru, masuk ke folder aplikasi:
   ```bash
   cd c:\xampp\htdocs\aitourism\itinerary-planner-tourism
   ```
2. Install dependensi PHP & Node.js:
   ```bash
   composer install
   npm install
   ```
3. Sesuaikan konfigurasi di `.env`:
   ```env
   # Ganti koneksi database sesuai environment (misal MySQL atau sqlite)
   DB_CONNECTION=sqlite

   # Konfigurasi Gemini API
   GEMINI_API_KEY="your-gemini-api-key-here"

   # Konfigurasi Chroma DB
   CHROMA_DB_URL="http://localhost:8002/api/v1"
   CHROMA_DB_COLLECTION="tourism_knowledge_base"
   ```
4. Jalankan Migrasi Database untuk membuat tabel-tabel termasuk tabel untuk Knowledge Base (vektor):
   ```bash
   php artisan migrate
   ```

### Tahap 3: Menelan Dataset (Ingestion) untuk RAG
Sistem ini menggunakan teknik *Retrieval-Augmented Generation* (RAG) untuk membaca dataset. Dataset dalam bentuk format Excel (`.xlsx`) perlu dikonversi ke vektor embedding agar relevansinya bisa dicari saat AI merancang itinerary.

1. Pastikan file Excel tersedia di `app/Storage/Dataset_Toba_Normalized.xlsx` (atau ubah argumen sesuai lokasi aslinya).
2. Pastikan server **Chroma DB** (port 8002) sedang berjalan!
3. Jalankan perintah ingest (Terminal Laravel):
   ```bash
   php artisan rag:ingest "app/Storage/Dataset_Toba_Normalized.xlsx"
   ```
4. Proses ini akan membaca setiap baris Excel, membuat vektor, lalu menyimpannya secara otomatis ke **Chroma DB**. 
   *(Tunggu hingga indikator progress 100% Selesai)*.

### Tahap 4: Menjalankan Frontend & Backend
Aplikasi membutuhkan terminal Laravel dan Vite yang berjalan bersamaan.

**Terminal 1 — Backend Laravel:**
```bash
php artisan serve
```
*(Server backend berjalan di `http://127.0.0.1:8000`)*

**Terminal 2 — Frontend (Vite / Tailwind CSS):**
```bash
npm run dev
```
*(Server frontend akan me-reload CSS/JS secara live saat ada perubahan file Blade/CSS)*

Buka browser kamu dan navigasi ke: **[http://localhost:8000](http://localhost:8000)**. 
Selamat! Aplikasi NusantaraAI kini berjalan!

---

## 🗺️ Alur Kerja Sistem AI (RAG Process)

```
[User Input: "Buat itinerary wisata..."]
        │
        ▼
1. Backend meng-generate 
   "Embedding Vector" dari input user via 
   Gemini API.
        │
        ▼
2. Vector Search (RAG): Mencari data di dalam
   Chroma DB yang paling relevan (kosinus) 
   dengan pertanyaan user.
        │
        ▼
3. Merakit Prompt: Menggabungkan Input User + 
   Konteks Data RAG yang didapat (Data Destinasi)
   + System Prompt utama.
        │
        ▼
4. Inference Chat: Mengirim prompt utuh yang 
   sudah disuntikkan konteks ke Gemini API.
        │
        ▼
5. Output JSON Itinerary diterima dan di-render 
   secara visual ke Dashboard.
```

---

## 🛠️ Troubleshooting (Kendala Umum)

### ❌ Ingestion Dataset Gagal / Error "Memory Size Exhausted"
- **Penyebab**: Proses parsing XLSX besar memakan RAM (batas default PHP biasanya 128MB).
- **Solusi**: Command `rag:ingest` sudah ditambahkan baris `ini_set('memory_limit', '-1')` agar batas memori terbuka. Pastikan tidak menghapus baris tersebut. Jika masih gagal, cek apakah file Excel memiliki ribuan row kosong yang ikut terbaca.

### ❌ AI Tidak Merespons
- **Solusi**: Pastikan kunci API Gemini (`GEMINI_API_KEY`) di file `.env` sudah benar dan kuota API Anda masih tersedia.

### ❌ Itinerary Tidak Menggunakan Data dari Dataset
- **Solusi**: Pastikan proses Ingestion sukses 100%. Cek kembali logika controller apakah hasil Vector Search RAG sudah digabungkan secara benar ke prompt sebelum dikirim ke Gemini.

---

Dibuat dengan ❤️ untuk **UI/UX Hackathon AI Tourism** 🌴
