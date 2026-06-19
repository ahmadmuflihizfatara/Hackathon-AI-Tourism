<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini menyimpan data destinasi wisata fix per provinsi.
     * Gambar disimpan sebagai path lokal (storage/app/public/destinations/)
     * atau URL eksternal (Wikimedia, dsb).
     */
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();

            // Identitas destinasi
            $table->string('slug')->unique()->comment('URL-friendly identifier, e.g. jembatan-ampera');
            $table->string('name')->comment('Nama destinasi resmi, e.g. Jembatan Ampera');
            $table->string('province')->index()->comment('Nama provinsi, e.g. Sumatera Selatan');
            $table->string('city')->nullable()->comment('Kota/kabupaten, e.g. Palembang');

            // Gambar utama — bisa path lokal ATAU URL eksternal
            $table->string('image_path')->nullable()
                  ->comment('Path relatif dari storage/app/public, e.g. destinations/jembatan-ampera.jpg');
            $table->string('image_url')->nullable()
                  ->comment('URL eksternal sebagai fallback, e.g. dari Wikimedia Commons');
            $table->string('image_credit')->nullable()
                  ->comment('Kredit foto, e.g. "© Wikimedia Commons / CC BY-SA 4.0"');

            // Metadata
            $table->string('category')->default('wisata')
                  ->comment('Kategori: wisata, kuliner, budaya, alam, pantai, belanja, museum');
            $table->text('description')->nullable()
                  ->comment('Deskripsi singkat destinasi (muncul di tooltip/card)');
            $table->string('lat', 20)->nullable()->comment('Koordinat latitude');
            $table->string('lng', 20)->nullable()->comment('Koordinat longitude');

            // Kontrol
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('priority')->default(50)
                  ->comment('Prioritas tampil: 1=tertinggi, 100=terendah');

            $table->timestamps();

            // Index untuk query cepat berdasarkan province+name
            $table->index(['province', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
