# 🌴 NusantaraAI — Tourism Itinerary Planner (Local LLM / RAG Version)

Aplikasi web perencanaan wisata Indonesia berbasis AI menggunakan **Laravel 11**, **Blade**, **Tailwind CSS**, dan **Local LLM (LM Studio + Gemma 2 + BGE-M3 RAG)**.

Versi ini tidak lagi menggunakan Google Gemini API, melainkan berjalan secara lokal sepenuhnya (Local AI) menjaga privasi data, serta memanfaatkan teknologi *Retrieval-Augmented Generation* (RAG) untuk memberikan data tempat wisata yang lebih akurat sesuai dataset (XLSX).

---

## 🎨 Desain & Teknologi

| Elemen | Nilai |
|---|---|
| **Framework** | Laravel 11 + Blade |
| **CSS** | Tailwind CSS v3 + Vite |
| **AI Backend** | LM Studio (Local LLM API) |
| **LLM Model** | Gemma 4 (e4b) / Gemma 2 |
| **Embedding Model**| BGE-M3 (BAAI) |
| **Database RAG** | Database Relasional via Laravel |
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
│   │   └── KnowledgeBase.php        ← Model untuk menyimpan vektor dataset
│   ├── Services/
│   │   └── LmStudioService.php      ← Logika komunikasi ke LM Studio (Chat & Embedding)
├── database/
│   └── migrations/
│       └── ...create_knowledge_bases_table.php ← Skema tabel vector RAG
├── storage/
│   └── app/Storage/
│       └── Dataset_Toba_Normalized.xlsx ← Dataset acuan RAG
```

---

## 🚀 Langkah-langkah Instalasi & Menjalankan Aplikasi

Ikuti panduan berikut secara berurutan agar aplikasi dan Local LLM berjalan sinkron.

### Tahap 1: Persiapan Local LLM (LM Studio)
1. **Download dan Install LM Studio** dari [https://lmstudio.ai/](https://lmstudio.ai/).
2. Buka LM Studio, pergi ke kolom pencarian (Search).
3. Cari dan download dua model berikut:
   - **Gemma 4 e4b / Gemma 2** dengan format GGUF (sebagai model utama obrolan/reasoning).
   - **BGE-M3** (sebagai model Text Embedding untuk vektor RAG).
4. Masuk ke tab **Local Server** (ikon ↔️ di kiri).
5. Load (muat) model Gemma pada slot Text/Chat Model.
6. Pastikan kapabilitas Text Embeddings aktif dan Load model BGE-M3 (terutama jika menggunakan multi-model setup pada LM Studio terbaru).
7. Klik tombol **Start Server**. Server API akan berjalan pada `http://127.0.0.1:1234/v1`.

### Tahap 2: Persiapan Aplikasi Laravel
1. Buka terminal, masuk ke folder aplikasi:
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
   # Ganti koneksi database sesuai environment (misal MySQL)
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_kamu
   DB_USERNAME=root
   DB_PASSWORD=

   # Konfigurasi LM Studio Local AI
   LMSTUDIO_API_URL=http://127.0.0.1:1234/v1
   LMSTUDIO_CHAT_MODEL=gemma-4-e4b
   LMSTUDIO_EMBEDDING_MODEL=bge-m3
   ```
4. Jalankan Migrasi Database untuk membuat tabel-tabel termasuk tabel untuk Knowledge Base (vektor):
   ```bash
   php artisan migrate
   ```

### Tahap 3: Menelan Dataset (Ingestion) untuk RAG
Sistem ini menggunakan teknik *Retrieval-Augmented Generation* (RAG) untuk membaca dataset. Dataset dalam bentuk format Excel (`.xlsx`) perlu dikonversi ke vektor embedding agar relevansinya bisa dicari saat AI merancang itinerary.

1. Pastikan file Excel tersedia di `app/Storage/Dataset_Toba_Normalized.xlsx` (atau ubah argumen sesuai lokasi aslinya).
2. Jalankan perintah ingest (Terminal):
   ```bash
   php artisan rag:ingest "app/Storage/Dataset_Toba_Normalized.xlsx"
   ```
3. Proses ini akan membaca setiap baris Excel, mengirimkannya ke LM Studio untuk diubah menjadi *embedding vector*, lalu menyimpannya ke database. 
   *(Tunggu hingga indikator progress 100% Selesai)*.

### Tahap 4: Menjalankan Frontend & Backend
Aplikasi membutuhkan 2 terminal yang berjalan secara bersamaan.

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
Selamat! Aplikasi NusantaraAI dengan AI lokal kini berjalan!

---

## 🗺️ Alur Kerja Sistem AI (RAG Process)

```
[User Input: "Buat itinerary wisata..."]
        │
        ▼
1. Backend (LmStudioService) meng-generate 
   "Embedding Vector" dari input user via 
   LM Studio (menggunakan BGE-M3).
        │
        ▼
2. Vector Search (RAG): Mencari data di tabel
   KnowledgeBase yang paling relevan (kosinus) 
   dengan pertanyaan user.
        │
        ▼
3. Merakit Prompt: Menggabungkan Input User + 
   Konteks Data RAG yang didapat (Data Destinasi)
   + System Prompt utama.
        │
        ▼
4. Inference Chat: Mengirim prompt utuh yang 
   sudah disuntikkan konteks ke LM Studio 
   (Model Gemma).
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

### ❌ AI Tidak Merespons / "Maaf, terjadi kesalahan saat menghubungi AI Lokal"
- **Solusi**: 
  1. Pastikan tombol hijau **Start Server** di LM Studio menyala.
  2. Pastikan port sesuai antara LM Studio (biasanya 1234) dengan variabel `LMSTUDIO_API_URL` di `.env`.
  3. Buka `http://127.0.0.1:1234/v1/models` di browser. Jika memunculkan file JSON, berarti server lokal menyala.

### ❌ Itinerary Tidak Menggunakan Data dari Dataset
- **Solusi**: Pastikan proses Ingestion (Tahap 3) sukses 100%. Pastikan LM Studio mengizinkan endpoint `/v1/embeddings` pada BGE-M3. Cek kembali `LmStudioService.php` apakah logika Vector Search RAG sudah digabungkan secara benar ke prompt sebelum dikirim ke chat completions.

---

Dibuat dengan ❤️ untuk **UI/UX Hackathon AI Tourism** 🌴 (Local LLM Edition)
