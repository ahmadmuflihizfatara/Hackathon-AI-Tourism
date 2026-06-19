<?php

namespace App\Services;

use App\Models\Destination;
use Illuminate\Support\Facades\Cache;

/**
 * DestinationImageService
 *
 * Service layer untuk mendapatkan gambar yang tepat bagi setiap destinasi.
 * Alur prioritas:
 *   1. Database lokal (tabel destinations) → gambar fix, akurat
 *   2. Cache (jika sudah pernah dicari)
 *   3. null  → frontend fallback ke Wikipedia API seperti biasa
 */
class DestinationImageService
{
    /**
     * Durasi cache dalam detik (24 jam)
     */
    private const CACHE_TTL = 86400;

    /**
     * Ambil URL gambar untuk suatu nama tempat.
     *
     * @param  string  $placeName  Nama destinasi dari output Gemini, e.g. "Jembatan Ampera"
     * @return array{url: string|null, credit: string|null, source: string}
     */
    public function getImageForPlace(string $placeName): array
    {
        $cacheKey = 'dest_img:' . md5(strtolower(trim($placeName)));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($placeName) {
            $destination = Destination::findByPlaceName($placeName);

            if ($destination && $destination->image_src) {
                return [
                    'url'    => $destination->image_src,
                    'credit' => $destination->image_credit,
                    'source' => 'database',
                ];
            }

            // Tidak ada di DB → frontend gunakan Wikipedia fallback
            return [
                'url'    => null,
                'credit' => null,
                'source' => 'fallback',
            ];
        });
    }

    /**
     * Batch lookup untuk seluruh aktivitas dalam satu itinerary.
     * Lebih efisien karena cukup 1 query DB per provinsi.
     *
     * @param  array  $placeNames  ['Jembatan Ampera', 'Pantai Losari', ...]
     * @return array<string, array{url: string|null, credit: string|null, source: string}>
     */
    public function getBatchImages(array $placeNames): array
    {
        $results = [];
        foreach ($placeNames as $name) {
            $results[$name] = $this->getImageForPlace($name);
        }
        return $results;
    }

    /**
     * Invalidate cache untuk satu destinasi (gunakan saat update data)
     */
    public function clearCache(string $placeName): void
    {
        $cacheKey = 'dest_img:' . md5(strtolower(trim($placeName)));
        Cache::forget($cacheKey);
    }
}
