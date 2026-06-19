<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * DestinationSeeder — data destinasi ikonik seluruh provinsi Indonesia.
 *
 * Struktur setiap entry:
 *   name       => Nama destinasi (dipakai untuk lookup dari AI response)
 *   province   => Nama provinsi (harus konsisten dengan output Gemini)
 *   city       => Kota/kabupaten
 *   image_url  => URL gambar dari Wikimedia Commons (resolusi tinggi, bebas lisensi)
 *   image_credit => Kredit foto
 *   category   => Kategori wisata
 *   description=> Deskripsi singkat
 *   lat / lng  => Koordinat GPS
 *   priority   => 1 = ikon paling terkenal (tampil pertama)
 */
class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = $this->getData();

        foreach ($destinations as $data) {
            DB::table('destinations')->updateOrInsert(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug'       => Str::slug($data['name']),
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ ' . count($destinations) . ' destinasi berhasil di-seed.');
    }

    private function getData(): array
    {
        return [
            // ── BALI ──────────────────────────────────────────────────────
            [
                'name'         => 'Tanah Lot',
                'province'     => 'Bali',
                'city'         => 'Tabanan',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/Tanah_Lot_Bali_Indonesia_Pura-Tanah-Lot-01.jpg/1280px-Tanah_Lot_Bali_Indonesia_Pura-Tanah-Lot-01.jpg',
                'image_credit' => '© Wikimedia Commons / Midori',
                'description'  => 'Pura laut ikonik Bali yang berdiri di atas batu karang di tepi laut.',
                'lat'          => '-8.6274',
                'lng'          => '115.1640',
                'priority'     => 1,
            ],
            [
                'name'         => 'Pura Besakih',
                'province'     => 'Bali',
                'city'         => 'Karangasem',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Pura_Besakih.jpg/1280px-Pura_Besakih.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pura terbesar dan tersuci di Bali, disebut "Ibu dari semua Pura".',
                'lat'          => '-8.3739',
                'lng'          => '115.4520',
                'priority'     => 2,
            ],
            [
                'name'         => 'Tegalalang Rice Terrace',
                'province'     => 'Bali',
                'city'         => 'Ubud',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Ceking_Rice_Terrace_Ubud_Bali_Indonesia.jpg/1280px-Ceking_Rice_Terrace_Ubud_Bali_Indonesia.jpg',
                'image_credit' => '© Wikimedia Commons / Yves Picq',
                'description'  => 'Sawah terasering spektakuler di Ubud dengan sistem irigasi subak.',
                'lat'          => '-8.4316',
                'lng'          => '115.2785',
                'priority'     => 3,
            ],
            [
                'name'         => 'Pantai Kuta',
                'province'     => 'Bali',
                'city'         => 'Badung',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Kuta_Beach_Bali.jpg/1280px-Kuta_Beach_Bali.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pantai paling terkenal di Bali, terkenal dengan sunset dan ombak surfing.',
                'lat'          => '-8.7245',
                'lng'          => '115.1720',
                'priority'     => 4,
            ],
            [
                'name'         => 'Pantai Double Six',
                'province'     => 'Bali',
                'city'         => 'Badung',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Seminyak_beach_sunset.jpg/1280px-Seminyak_beach_sunset.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pantai Seminyak yang ramai dengan bean bag warna-warni dan sunset menakjubkan.',
                'lat'          => '-8.6906',
                'lng'          => '115.1694',
                'priority'     => 5,
            ],
            [
                'name'         => 'Ubud',
                'province'     => 'Bali',
                'city'         => 'Gianyar',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Ubud_Palace.jpg/1280px-Ubud_Palace.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pusat seni dan budaya Bali dengan galeri, pasar, dan hutan monyet.',
                'lat'          => '-8.5069',
                'lng'          => '115.2625',
                'priority'     => 6,
            ],

            // ── JAKARTA ───────────────────────────────────────────────────
            [
                'name'         => 'Monas',
                'province'     => 'DKI Jakarta',
                'city'         => 'Jakarta Pusat',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/NationalMonumentJakarta.jpg/1280px-NationalMonumentJakarta.jpg',
                'image_credit' => '© Wikimedia Commons / Gunawan Kartapranata',
                'description'  => 'Monumen Nasional — lambang kemerdekaan Indonesia setinggi 132 meter.',
                'lat'          => '-6.1754',
                'lng'          => '106.8272',
                'priority'     => 1,
            ],
            [
                'name'         => 'Kepulauan Seribu',
                'province'     => 'DKI Jakarta',
                'city'         => 'Jakarta Utara',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Tidung_Island_Jakarta.jpg/1280px-Tidung_Island_Jakarta.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Gugusan 110 pulau cantik di Teluk Jakarta dengan snorkeling dan pasir putih.',
                'lat'          => '-5.6149',
                'lng'          => '106.5722',
                'priority'     => 2,
            ],
            [
                'name'         => 'Kota Tua Jakarta',
                'province'     => 'DKI Jakarta',
                'city'         => 'Jakarta Barat',
                'category'     => 'museum',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Jakarta_Old_Town_at_Night.jpg/1280px-Jakarta_Old_Town_at_Night.jpg',
                'image_credit' => '© Wikimedia Commons / Gunawan Kartapranata',
                'description'  => 'Kawasan bersejarah peninggalan Batavia era VOC dengan museum dan arsitektur kuno.',
                'lat'          => '-6.1352',
                'lng'          => '106.8133',
                'priority'     => 3,
            ],

            // ── YOGYAKARTA ────────────────────────────────────────────────
            [
                'name'         => 'Candi Borobudur',
                'province'     => 'Jawa Tengah',
                'city'         => 'Magelang',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Borobudur-Nothwest-view.jpg/1280px-Borobudur-Nothwest-view.jpg',
                'image_credit' => '© Wikimedia Commons / Gunawan Kartapranata',
                'description'  => 'Candi Buddha terbesar di dunia, Warisan Budaya UNESCO.',
                'lat'          => '-7.6079',
                'lng'          => '110.2038',
                'priority'     => 1,
            ],
            [
                'name'         => 'Candi Prambanan',
                'province'     => 'Daerah Istimewa Yogyakarta',
                'city'         => 'Sleman',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b9/Prambanan_temple.jpg/1280px-Prambanan_temple.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Kompleks candi Hindu terbesar di Indonesia, Warisan Budaya UNESCO.',
                'lat'          => '-7.7520',
                'lng'          => '110.4914',
                'priority'     => 2,
            ],
            [
                'name'         => 'Keraton Yogyakarta',
                'province'     => 'Daerah Istimewa Yogyakarta',
                'city'         => 'Yogyakarta',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/Yogyakarta_Kraton.jpg/1280px-Yogyakarta_Kraton.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Istana Kesultanan Yogyakarta, pusat budaya Jawa yang masih aktif.',
                'lat'          => '-7.8052',
                'lng'          => '110.3642',
                'priority'     => 3,
            ],
            [
                'name'         => 'Pantai Parangtritis',
                'province'     => 'Daerah Istimewa Yogyakarta',
                'city'         => 'Bantul',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b7/Parangtritis_Beach.jpg/1280px-Parangtritis_Beach.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pantai selatan Yogyakarta yang legendaris dengan gumuk pasir unik.',
                'lat'          => '-8.0250',
                'lng'          => '110.3330',
                'priority'     => 4,
            ],
            [
                'name'         => 'Gunung Merapi',
                'province'     => 'Daerah Istimewa Yogyakarta',
                'city'         => 'Sleman',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Mount_Merapi_from_Merbabu.jpg/1280px-Mount_Merapi_from_Merbabu.jpg',
                'image_credit' => '© Wikimedia Commons / Gunawan Kartapranata',
                'description'  => 'Gunung berapi paling aktif di Indonesia, wisata lava tour populer.',
                'lat'          => '-7.5407',
                'lng'          => '110.4457',
                'priority'     => 5,
            ],

            // ── SUMATERA SELATAN ──────────────────────────────────────────
            [
                'name'         => 'Jembatan Ampera',
                'province'     => 'Sumatera Selatan',
                'city'         => 'Palembang',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Ampera_Bridge_Palembang.jpg/1280px-Ampera_Bridge_Palembang.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Jembatan ikonik di atas Sungai Musi, simbol kota Palembang.',
                'lat'          => '-2.9918',
                'lng'          => '104.7630',
                'priority'     => 1,
            ],
            [
                'name'         => 'Danau Ranau',
                'province'     => 'Sumatera Selatan',
                'city'         => 'Ogan Komering Ulu Selatan',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Lake_Ranau_Sumatra.jpg/1280px-Lake_Ranau_Sumatra.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Danau vulkanik terbesar kedua di Sumatera dengan pemandangan menakjubkan.',
                'lat'          => '-4.8500',
                'lng'          => '103.9167',
                'priority'     => 2,
            ],

            // ── SUMATERA UTARA ────────────────────────────────────────────
            [
                'name'         => 'Danau Toba',
                'province'     => 'Sumatera Utara',
                'city'         => 'Samosir',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Lake_Toba_-_Sumatra_Indonesia.jpg/1280px-Lake_Toba_-_Sumatra_Indonesia.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Danau vulkanik terbesar di dunia dengan Pulau Samosir di tengahnya.',
                'lat'          => '2.6845',
                'lng'          => '98.8756',
                'priority'     => 1,
            ],
            [
                'name'         => 'Istana Maimun',
                'province'     => 'Sumatera Utara',
                'city'         => 'Medan',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Maimoon_Palace_Medan.jpg/1280px-Maimoon_Palace_Medan.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Istana Kesultanan Deli bergaya arsitektur Melayu-Islam yang megah.',
                'lat'          => '3.5753',
                'lng'          => '98.6861',
                'priority'     => 2,
            ],

            // ── ACEH ──────────────────────────────────────────────────────
            [
                'name'         => 'Masjid Raya Baiturrahman',
                'province'     => 'Aceh',
                'city'         => 'Banda Aceh',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Baiturrahman_Grand_Mosque_Banda_Aceh.jpg/1280px-Baiturrahman_Grand_Mosque_Banda_Aceh.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Masjid kebanggaan Aceh yang berdiri sejak abad ke-17 dan selamat dari tsunami 2004.',
                'lat'          => '5.5574',
                'lng'          => '95.3179',
                'priority'     => 1,
            ],
            [
                'name'         => 'Sabang',
                'province'     => 'Aceh',
                'city'         => 'Sabang',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Pantai_Iboih_Sabang.jpg/1280px-Pantai_Iboih_Sabang.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Ujung barat Indonesia dengan pantai kristal dan titik 0 Km Nusantara.',
                'lat'          => '5.5577',
                'lng'          => '95.3175',
                'priority'     => 2,
            ],

            // ── LOMBOK / NTB ──────────────────────────────────────────────
            [
                'name'         => 'Gunung Rinjani',
                'province'     => 'Nusa Tenggara Barat',
                'city'         => 'Lombok Utara',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e9/Mt_Rinjani_from_Senaru_Crater_Rim.jpg/1280px-Mt_Rinjani_from_Senaru_Crater_Rim.jpg',
                'image_credit' => '© Wikimedia Commons / Deni Williams',
                'description'  => 'Gunung berapi tertinggi kedua di Indonesia dengan Danau Segara Anak yang cantik.',
                'lat'          => '-8.4120',
                'lng'          => '116.4650',
                'priority'     => 1,
            ],
            [
                'name'         => 'Pantai Pink',
                'province'     => 'Nusa Tenggara Barat',
                'city'         => 'Lombok Timur',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Pink_Beach_Lombok.jpg/1280px-Pink_Beach_Lombok.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pantai berpasir merah muda langka yang hanya ada beberapa di dunia.',
                'lat'          => '-8.7500',
                'lng'          => '116.5833',
                'priority'     => 2,
            ],
            [
                'name'         => 'Gili Trawangan',
                'province'     => 'Nusa Tenggara Barat',
                'city'         => 'Lombok Utara',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Gili_trawangan.jpg/1280px-Gili_trawangan.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pulau kecil bebas kendaraan bermotor, surga snorkeling dan diving.',
                'lat'          => '-8.3564',
                'lng'          => '115.9736',
                'priority'     => 3,
            ],

            // ── NTT / LABUAN BAJO ─────────────────────────────────────────
            [
                'name'         => 'Taman Nasional Komodo',
                'province'     => 'Nusa Tenggara Timur',
                'city'         => 'Manggarai Barat',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Komodo_dragon_on_Rinca.jpg/1280px-Komodo_dragon_on_Rinca.jpg',
                'image_credit' => '© Wikimedia Commons / Raul654',
                'description'  => 'Rumah asli Komodo, kadal terbesar di dunia. Warisan Alam UNESCO.',
                'lat'          => '-8.5500',
                'lng'          => '119.4833',
                'priority'     => 1,
            ],
            [
                'name'         => 'Pantai Pink Komodo',
                'province'     => 'Nusa Tenggara Timur',
                'city'         => 'Manggarai Barat',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Pink_beach_Komodo.jpg/1280px-Pink_beach_Komodo.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pantai berpasir pink di kawasan Taman Nasional Komodo.',
                'lat'          => '-8.6200',
                'lng'          => '119.5800',
                'priority'     => 2,
            ],

            // ── RAJA AMPAT / PAPUA BARAT ──────────────────────────────────
            [
                'name'         => 'Raja Ampat',
                'province'     => 'Papua Barat Daya',
                'city'         => 'Raja Ampat',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Raja_Ampat_Islands.jpg/1280px-Raja_Ampat_Islands.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Surga bahari Indonesia dengan keanekaragaman hayati laut terkaya di dunia.',
                'lat'          => '-0.5000',
                'lng'          => '130.5000',
                'priority'     => 1,
            ],

            // ── SULAWESI SELATAN ──────────────────────────────────────────
            [
                'name'         => 'Pantai Losari',
                'province'     => 'Sulawesi Selatan',
                'city'         => 'Makassar',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Losari_Beach_Makassar.jpg/1280px-Losari_Beach_Makassar.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pantai ikon Makassar, terkenal dengan sunsetnya yang memukau.',
                'lat'          => '-5.1341',
                'lng'          => '119.4048',
                'priority'     => 1,
            ],
            [
                'name'         => 'Tana Toraja',
                'province'     => 'Sulawesi Selatan',
                'city'         => 'Tana Toraja',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c7/Tongkonan_Tana_Toraja.jpg/1280px-Tongkonan_Tana_Toraja.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Budaya unik suku Toraja dengan rumah adat Tongkonan dan ritual Rambu Solo.',
                'lat'          => '-3.0500',
                'lng'          => '119.8667',
                'priority'     => 2,
            ],

            // ── KALIMANTAN TIMUR ──────────────────────────────────────────
            [
                'name'         => 'Derawan Island',
                'province'     => 'Kalimantan Timur',
                'city'         => 'Berau',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Derawan_Island_Berau.jpg/1280px-Derawan_Island_Berau.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Surga penyelaman dengan danau ubur-ubur tak menyengat yang langka di dunia.',
                'lat'          => '2.2833',
                'lng'          => '118.2500',
                'priority'     => 1,
            ],

            // ── JAWA BARAT ────────────────────────────────────────────────
            [
                'name'         => 'Kawah Putih',
                'province'     => 'Jawa Barat',
                'city'         => 'Bandung',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Kawah_Putih_Ciwidey_West_Java_Indonesia.jpg/1280px-Kawah_Putih_Ciwidey_West_Java_Indonesia.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Danau kawah vulkanik berwarna putih kehijauan di ketinggian 2.430 mdpl.',
                'lat'          => '-7.1660',
                'lng'          => '107.4024',
                'priority'     => 1,
            ],
            [
                'name'         => 'Gunung Tangkuban Perahu',
                'province'     => 'Jawa Barat',
                'city'         => 'Subang',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8f/Tangkuban_Perahu_volcano.jpg/1280px-Tangkuban_Perahu_volcano.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Gunung berapi unik berbentuk perahu terbalik dengan kawah belerang aktif.',
                'lat'          => '-6.7700',
                'lng'          => '107.6096',
                'priority'     => 2,
            ],

            // ── JAWA TENGAH ───────────────────────────────────────────────
            [
                'name'         => 'Lawang Sewu',
                'province'     => 'Jawa Tengah',
                'city'         => 'Semarang',
                'category'     => 'museum',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Lawang_Sewu_at_night.jpg/1280px-Lawang_Sewu_at_night.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Gedung bersejarah peninggalan Belanda dengan ratusan pintu yang ikonik.',
                'lat'          => '-6.9845',
                'lng'          => '110.4125',
                'priority'     => 1,
            ],

            // ── JAWA TIMUR ────────────────────────────────────────────────
            [
                'name'         => 'Gunung Bromo',
                'province'     => 'Jawa Timur',
                'city'         => 'Probolinggo',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Bromo_tengger_semeru_kk.jpg/1280px-Bromo_tengger_semeru_kk.jpg',
                'image_credit' => '© Wikimedia Commons / Rendra Kurniawan',
                'description'  => 'Gunung berapi aktif di lautan pasir Tengger, sunrise paling dramatis di Indonesia.',
                'lat'          => '-7.9425',
                'lng'          => '112.9530',
                'priority'     => 1,
            ],
            [
                'name'         => 'Air Terjun Tumpak Sewu',
                'province'     => 'Jawa Timur',
                'city'         => 'Lumajang',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e9/Tumpak_Sewu_Waterfall.jpg/1280px-Tumpak_Sewu_Waterfall.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Air terjun tirai selebar 120 meter, dijuluki "Niagara-nya Indonesia".',
                'lat'          => '-8.2308',
                'lng'          => '112.9097',
                'priority'     => 2,
            ],

            // ── MALUKU ────────────────────────────────────────────────────
            [
                'name'         => 'Pantai Ora',
                'province'     => 'Maluku',
                'city'         => 'Seram Bagian Barat',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/Ora_Beach_Maluku.jpg/1280px-Ora_Beach_Maluku.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pantai tersembunyi dengan air jernih berwarna toska dan bungalow di atas air.',
                'lat'          => '-3.0833',
                'lng'          => '129.0000',
                'priority'     => 1,
            ],
            [
                'name'         => 'Benteng Belgica',
                'province'     => 'Maluku',
                'city'         => 'Banda Naira',
                'category'     => 'museum',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b7/Belgica_Fort_Banda_Naira.jpg/1280px-Belgica_Fort_Banda_Naira.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Benteng VOC abad 17 di kepulauan rempah Banda Naira yang bersejarah.',
                'lat'          => '-4.5251',
                'lng'          => '129.9058',
                'priority'     => 2,
            ],

            // ── SULAWESI UTARA ────────────────────────────────────────────
            [
                'name'         => 'Taman Nasional Bunaken',
                'province'     => 'Sulawesi Utara',
                'city'         => 'Manado',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Bunaken_National_Marine_Park.jpg/1280px-Bunaken_National_Marine_Park.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Taman laut internasional dengan wall diving spektakuler dan keanekaragaman terumbu karang.',
                'lat'          => '1.6200',
                'lng'          => '124.7500',
                'priority'     => 1,
            ],

            // ── BANGKA BELITUNG ───────────────────────────────────────────
            [
                'name'         => 'Pantai Tanjung Tinggi',
                'province'     => 'Kepulauan Bangka Belitung',
                'city'         => 'Belitung',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Tanjung_Tinggi_Beach_Belitung.jpg/1280px-Tanjung_Tinggi_Beach_Belitung.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pantai dengan batu granit raksasa yang ikonik, lokasi syuting Laskar Pelangi.',
                'lat'          => '-2.5500',
                'lng'          => '107.6667',
                'priority'     => 1,
            ],

            // ── KALIMANTAN BARAT ──────────────────────────────────────────
            [
                'name'         => 'Danau Sentarum',
                'province'     => 'Kalimantan Barat',
                'city'         => 'Kapuas Hulu',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Danau_Sentarum.jpg/1280px-Danau_Sentarum.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Taman Nasional danau musiman unik yang menjadi habitat ikan arwana.',
                'lat'          => '0.8833',
                'lng'          => '112.0167',
                'priority'     => 1,
            ],

            // ── SUMATERA BARAT ────────────────────────────────────────────
            [
                'name'         => 'Jam Gadang',
                'province'     => 'Sumatera Barat',
                'city'         => 'Bukittinggi',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Jam_Gadang_Bukittinggi.jpg/1280px-Jam_Gadang_Bukittinggi.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Menara jam ikonik Bukittinggi dengan arsitektur khas Minangkabau.',
                'lat'          => '-0.3069',
                'lng'          => '100.3694',
                'priority'     => 1,
            ],
            [
                'name'         => 'Danau Maninjau',
                'province'     => 'Sumatera Barat',
                'city'         => 'Agam',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f5/Lake_Maninjau_West_Sumatra.jpg/1280px-Lake_Maninjau_West_Sumatra.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Danau vulkanik di cekungan indah Minangkabau dengan kelok 44 yang legendaris.',
                'lat'          => '-0.3167',
                'lng'          => '100.2000',
                'priority'     => 2,
            ],

            // ── RIAU ──────────────────────────────────────────────────────
            [
                'name'         => 'Pulau Bintan',
                'province'     => 'Kepulauan Riau',
                'city'         => 'Bintan',
                'category'     => 'pantai',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e2/Bintan_Island_Beach.jpg/1280px-Bintan_Island_Beach.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Resort island mewah berdekatan Singapura dengan pantai pasir putih.',
                'lat'          => '1.2167',
                'lng'          => '104.4833',
                'priority'     => 1,
            ],

            // ── LAMPUNG ───────────────────────────────────────────────────
            [
                'name'         => 'Taman Nasional Way Kambas',
                'province'     => 'Lampung',
                'city'         => 'Lampung Timur',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Sumatran_elephant_Way_Kambas.jpg/1280px-Sumatran_elephant_Way_Kambas.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Suaka gajah Sumatera, harimau, dan badak bercula satu yang terancam punah.',
                'lat'          => '-4.9833',
                'lng'          => '105.6167',
                'priority'     => 1,
            ],

            // ── NUSA TENGGARA TIMUR ───────────────────────────────────────
            [
                'name'         => 'Kelimutu',
                'province'     => 'Nusa Tenggara Timur',
                'city'         => 'Ende',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/54/Kelimutu_NTT_2012.jpg/1280px-Kelimutu_NTT_2012.jpg',
                'image_credit' => '© Wikimedia Commons / Wikichubbah',
                'description'  => 'Tiga danau kawah dengan warna berbeda yang unik di dunia, Flores, NTT.',
                'lat'          => '-8.7713',
                'lng'          => '121.8203',
                'priority'     => 1,
            ],

            // ── KALIMANTAN SELATAN ────────────────────────────────────────
            [
                'name'         => 'Pasar Terapung Lok Baintan',
                'province'     => 'Kalimantan Selatan',
                'city'         => 'Banjarmasin',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Floating_Market_Lok_Baintan.jpg/1280px-Floating_Market_Lok_Baintan.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Pasar tradisional di atas perahu jukung di Sungai Martapura.',
                'lat'          => '-3.3667',
                'lng'          => '114.6667',
                'priority'     => 1,
            ],

            // ── GORONTALO ─────────────────────────────────────────────────
            [
                'name'         => 'Taman Nasional Bogani Nani Wartabone',
                'province'     => 'Gorontalo',
                'city'         => 'Bone Bolango',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Bogani_Nani_Wartabone.jpg/1280px-Bogani_Nani_Wartabone.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Taman nasional terbesar di Sulawesi, habitat anoa dan maleo.',
                'lat'          => '0.5500',
                'lng'          => '122.8333',
                'priority'     => 1,
            ],

            // ── PAPUA ─────────────────────────────────────────────────────
            [
                'name'         => 'Puncak Jaya',
                'province'     => 'Papua Tengah',
                'city'         => 'Puncak Jaya',
                'category'     => 'alam',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Carstensz_Pyramid_Papua.jpg/1280px-Carstensz_Pyramid_Papua.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Puncak tertinggi di Indonesia (4.884 mdpl) dan satu-satunya gletser tropis.',
                'lat'          => '-4.0833',
                'lng'          => '137.1833',
                'priority'     => 1,
            ],

            // ── BENGKULU ──────────────────────────────────────────────────
            [
                'name'         => 'Benteng Marlborough',
                'province'     => 'Bengkulu',
                'city'         => 'Bengkulu',
                'category'     => 'museum',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Fort_Marlborough_Bengkulu.jpg/1280px-Fort_Marlborough_Bengkulu.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Benteng Inggris terbesar di Asia Tenggara, dibangun tahun 1713-1719.',
                'lat'          => '-3.7945',
                'lng'          => '102.2575',
                'priority'     => 1,
            ],

            // ── JAMBI ─────────────────────────────────────────────────────
            [
                'name'         => 'Candi Muaro Jambi',
                'province'     => 'Jambi',
                'city'         => 'Muaro Jambi',
                'category'     => 'budaya',
                'image_url'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/39/Muaro_Jambi_Temple.jpg/1280px-Muaro_Jambi_Temple.jpg',
                'image_credit' => '© Wikimedia Commons',
                'description'  => 'Kompleks percandian Buddha terluas di Asia Tenggara dari abad ke-11.',
                'lat'          => '-1.4850',
                'lng'          => '103.7892',
                'priority'     => 1,
            ],
        ];
    }
}
