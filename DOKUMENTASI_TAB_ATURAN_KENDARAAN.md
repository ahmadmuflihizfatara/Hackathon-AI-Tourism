# 📋 NusantaraAI Tab Aturan & Kendaraan — Dokumentasi Komponen

## 🎯 Overview

Komponen UI dinamis untuk menampilkan:
1. **Tab Aturan** — Regulasi lokal, hukum adat, keselamatan di destinasi wisata
2. **Tab Kendaraan** — Rekomendasi moda transportasi optimal berdasarkan kondisi jalan

Kedua tab menggunakan **Tailwind CSS**, **Material Icons**, dan struktur data JSON yang fleksibel.

---

## 📦 Struktur Komponen

### 1. Tab Aturan (`renderAturan()`)

**Lokasi file:** `resources/views/pages/dashboard.blade.php` (lines ~660-720)

**Fungsi utama:**
```javascript
renderAturan(rules)  // Array dari object aturan
renderAturanLine(r)  // Single rule item
getAturanIcon(category)
getAturanBgColor(category)
getAturanIconColor(category)
```

**Format data yang diharapkan:**
```json
[
  {
    "place": "Nama Tempat Wisata",
    "category": "religi|pantai|gunung|konservasi|cagar budaya|desa adat|air terjun|taman nasional",
    "rules": [
      { "text": "Deskripsi aturan", "type": "wajib|larangan|anjuran|peringatan" }
    ],
    "source_note": "Sumber kebijakan (opsional)"
  }
]
```

**Kategori & Warna:**
- `religi` → Kuning (text-yellow-600, bg-yellow-100)
- `pantai` → Cyan (text-cyan-600, bg-cyan-100)
- `gunung` → Hijau (text-green-600, bg-green-100)
- `konservasi` → Emerald (text-emerald-600, bg-emerald-100)
- `cagar budaya` → Ungu (text-purple-600, bg-purple-100)
- `desa adat` → Amber (text-amber-600, bg-amber-100)
- `air terjun` → Biru (text-blue-600, bg-blue-100)
- `taman nasional` → Teal (text-teal-600, bg-teal-100)

**Tipe Aturan:**
- `wajib` → ✓ Check Circle (Blue) — Harus dilakukan
- `larangan` → ✗ Cancel (Rose) — Dilarang keras
- `anjuran` → 💡 Lightbulb (Emerald) — Direkomendasikan
- `peringatan` → ⚠️ Warning (Amber) — Alert penting

---

### 2. Tab Kendaraan (`renderKendaraan()`)

**Lokasi file:** `resources/views/pages/dashboard.blade.php` (lines ~730-810)

**Fungsi utama:**
```javascript
renderKendaraan(items)   // Array dari object kendaraan
getVehicleBadge(type)
getRoadBadge(condition)
formatPriceRange(min, max)
```

**Format data yang diharapkan:**
```json
[
  {
    "segment": "Deskripsi rute / aktivitas perjalanan",
    "vehicle_type": "motor|mobil pribadi|kendaraan umum|kapal/perahu|jalan kaki",
    "reason": "Penjelasan mengapa kendaraan ini direkomendasikan",
    "road_condition": "akses mudah|akses sedang|akses terbatas",
    "public_transport": {
      "available": true,
      "options": [
        {
          "name": "Nama Moda Transportasi",
          "icon": "material_icon_name",
          "price_min": 25000,
          "price_max": 50000,
          "duration": "1 jam (opsional)",
          "note": "Catatan tambahan (opsional)"
        }
      ]
    }
  }
]
```

**Tipe Kendaraan & Badge:**
| Tipe | Icon | Warna | Kegunaan |
|------|------|-------|----------|
| `motor` | two_wheeler | Amber | Akses sempit, biaya rendah |
| `mobil pribadi` | directions_car | Blue | Jarak jauh, nyaman |
| `kendaraan umum` | directions_bus | Emerald | Budget, kapasitas besar |
| `kapal/perahu` | directions_boat | Cyan | Rute air |
| `jalan kaki` | directions_walk | Stone | Destinasi dekat |

**Kondisi Jalan & Badge:**
- `akses mudah` → ✓ Akses Mudah (Emerald) — Highway/jalan raya
- `akses sedang` → ◐ Akses Sedang (Amber) — Jalan lokal
- `akses terbatas` → ⚠ Akses Terbatas (Rose) — Jalan rusak/sempit

---

## 🔄 Flow Data

```
API Gemini
    ↓
GeminiService::chat() 
    ↓
Parsing JSON (termasuk "aturan" & "kendaraan")
    ↓
JavaScript renderItinerary()
    ↓
renderAturan() + renderKendaraan()
    ↓
DOM di-update dengan HTML dinamis
```

---

## 🛠️ Implementasi

### A. Di Backend (API)

**File:** `app/Services/GeminiService.php`

Prompt Gemini sudah diupdate untuk menginstruksikan:
1. Tambahkan field `"aturan": [...]` dalam respons itinerary
2. Tambahkan field `"kendaraan": [...]` dalam respons itinerary
3. Ikuti format data yang sudah ditentukan

**Instruksi akan dikirim ke Gemini:**
- Berapa banyak item aturan? 2-4 untuk destinasi utama
- Berapa banyak item kendaraan? 1-3 untuk segmen penting
- Type apa saja untuk aturan? wajib, larangan, anjuran, peringatan
- Vehicle type apa saja? motor, mobil, umum, kapal, jalan kaki

### B. Di Frontend (Blade / JavaScript)

**File:** `resources/views/pages/dashboard.blade.php`

```javascript
// Dipanggil saat itinerary muncul:
renderItinerary(data) {
    // ...
    renderAturan(data.aturan || []);      // Tab Aturan
    renderKendaraan(data.kendaraan || []); // Tab Kendaraan
    // ...
}

// Saat user klik tab "Aturan" atau "Kendaraan"
switchTab('aturan')   // Tampil #tab-aturan
switchTab('kendaraan') // Tampil #tab-kendaraan
```

---

## 📝 Contoh Payload Test

File: `TEST_ATURAN_KENDARAAN.json`

Berisi itinerary lengkap untuk **Yogyakarta 3 hari** dengan:
- 3 item aturan (Borobudur, Desa Kasongan, Gunung Merapi)
- 3 item kendaraan (Airport→Hotel, Hotel→Borobudur, Kota→Kasongan)
- Schedule, budget, tips

**Cara test:**
1. Buka DevTools → Network
2. Test API endpoint manual atau tunggu respons Gemini
3. Bandingkan struktur data dengan file ini
4. Verifikasi rendering tab-aturan dan tab-kendaraan

---

## 🎨 Desain & UX Features

### Tab Aturan
✅ Card bergradien dengan border highlight  
✅ Ikon representatif per kategori  
✅ Badge tipe aturan (wajib/larangan/anjuran/peringatan)  
✅ Info banner pentingnya dengan konteks UNESCO/legal  
✅ Hover effect scale ikon  
✅ Source note untuk kredibilitas  
✅ Empty state dengan ilustrasi  

### Tab Kendaraan
✅ Card gradient dengan road condition badge  
✅ Ikon kendaraan berbeda-beda  
✅ Sub-section untuk opsi transportasi umum  
✅ Price range per opsi + duration + catatan  
✅ Warning alert jika tidak ada transportasi umum  
✅ Tip banner tentang cara memilih kendaraan  
✅ Empty state dengan ilustrasi  

---

## 🚀 Cara Menggunakan

### 1. Testing Manual
```bash
# Buka browser
http://127.0.0.1:8000/dashboard

# Ketik di chat:
"Saya mau ke Yogyakarta 3 hari, budget Rp 3.5 juta, suka budaya dan alam"

# Tunggu respons Gemini
# Cek tab "Aturan" dan "Kendaraan" untuk melihat data dinamis
```

### 2. Verifikasi Data
```javascript
// Di DevTools Console:
console.log(currentItinerary.aturan)
console.log(currentItinerary.kendaraan)
```

### 3. Debug
```javascript
// Jika tab tidak muncul:
document.getElementById('tab-aturan') // Harus visible
document.getElementById('tab-kendaraan') // Harus visible

// Jika data tidak render:
// Buka Network tab, check API response dari /api/gemini
// Pastikan JSON valid: https://jsonlint.com
```

---

## 📌 Catatan Penting

1. **Data Optional** — Field "aturan" dan "kendaraan" bersifat opsional dalam respons Gemini
2. **Format Fleksibel** — Komponen dapat handle berbagai format data
3. **Empty State** — Jika tidak ada data, tampil pesan dan ikon placeholder
4. **Responsive** — Desain sudah mobile-friendly (tested di viewport berbagai ukuran)
5. **Accessibility** — Gunakan semantic HTML, ikon + text, contrast ratio WCAG AA

---

## 🔍 Troubleshooting

### Tab tidak muncul
- Periksa HTML ID: `id="tab-aturan"`, `id="tab-kendaraan"` harus ada
- Periksa `switchTab()` dipanggil: cek DevTools jika ada error

### Data tidak render
- Verifikasi format JSON dari API
- Cek console error di DevTools
- Buka test file `TEST_ATURAN_KENDARAAN.json` untuk referensi

### Warna tidak sesuai
- Periksa kategori spelling: case-sensitive
- Pastikan kategori ada di map `getAturanBgColor()`, `getAturanIconColor()`

### Icon tidak tampil
- Verifikasi Material Icons library sudah di-load (di `app.blade.php`)
- Cek nama icon di Material Icons docs: https://fonts.google.com/icons

---

## 📚 Referensi

- **Tailwind CSS:** https://tailwindcss.com
- **Material Icons:** https://fonts.google.com/icons
- **JSON Validator:** https://jsonlint.com
- **Prompt Gemini:** `app/Services/GeminiService.php` (buildSystemPrompt method)

---

## 🎬 Next Steps

1. ✅ Komponen UI sudah jadi
2. ✅ Prompt Gemini sudah diupdate
3. ⏳ Test dengan input pengguna nyata
4. ⏳ Fine-tune styling jika diperlukan
5. ⏳ Deploy ke production

**Catatan:** Pastikan `GEMINI_API_KEY` sudah di-set di `.env` untuk testing penuh!

---

**Created:** 2026-07-01  
**By:** GitHub Copilot  
**For:** NusantaraAI Hackathon Project
