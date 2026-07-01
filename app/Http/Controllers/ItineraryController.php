<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItineraryController extends Controller
{
    public function generate(Request $request)
    {
        $destinasi = $request->input('destinasi');

        // ponytail: mock data, swap dengan AI API call (Anthropic/Claude) nanti
        $data = [
            'destinasi' => $destinasi,
            'aturan' => [
                'regulasi' => [
                    "Wisatawan wajib membayar retribusi masuk Rp 25.000",
                    "Snorkeling hanya dengan pemandu bersertifikat",
                    "Dilarang mengambil karang atau biota laut sebagai suvenir"
                ],
                'keselamatan' => [
                    "Arus laut kuat di area tertentu, hindari area tanpa tanda",
                    "Jalur curam dan licin saat hujan, gunakan alas anti-slip",
                    "Sinyal terbatas, unduh peta offline sebelumnya"
                ],
                'adat' => [
                    "Kenakan sarung saat masuk area pura",
                    "Hindari foto upacara keagamaan tanpa izin",
                    "Jangan injak sesajen di jalan"
                ],
                'catatan_ai' => "Musim kemarau (April–Oktober) paling ideal untuk aktivitas outdoor."
            ],
            'kendaraan' => [
                'rekomendasi' => [
                    [
                        'nama' => 'Motor Matic',
                        'skor' => 5,
                        'cocok_untuk' => ['Jalan sempit & berkelok', 'Solo / berdua', 'Parkir terbatas'],
                        'alasan' => 'Paling lincah di medan berbukit dan tikungan tajam.'
                    ],
                    [
                        'nama' => 'Mobil Jeep 4x4',
                        'skor' => 4,
                        'cocok_untuk' => ['Rombongan 4–6 orang', 'Jalan berbatu', 'Musim hujan'],
                        'alasan' => 'Stabil di ruas berbatu & belum beraspal rata.'
                    ],
                    [
                        'nama' => 'Van / Minibus',
                        'skor' => 2,
                        'cocok_untuk' => ['Rombongan >8 orang', 'Jalan utama beraspal'],
                        'alasan' => 'Ukuran kurang ideal untuk jalan sempit pedesaan.'
                    ]
                ]
            ]
        ];

        return response()->json($data);
    }
}
