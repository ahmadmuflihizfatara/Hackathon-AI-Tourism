<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * DestinationImageService
 *
 * Mencari gambar destinasi wisata dengan fallback dua sumber:
 *   1. Unsplash API  — kualitas foto tertinggi, gratis 50 req/jam
 *   2. Pexels API    — backup berkualitas, gratis 200 req/jam
 *
 * Jika keduanya tidak menghasilkan gambar, kembalikan null
 * dan biarkan frontend memakai picsum placeholder.
 *
 * Setiap hasil di-cache 24 jam agar tidak membuang kuota API.
 */
class DestinationImageService
{
    private const UNSPLASH_WIDTH  = 1280;
    private const UNSPLASH_HEIGHT = 720;
    private const PEXELS_PER_PAGE = 5;
    private const CACHE_TTL       = 86400; // 24 jam

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Kembalikan data gambar untuk satu nama tempat.
     *
     * @return array{url: string|null, source: string, credit: string|null}
     */
    public function getImageForPlace(string $placeName): array
    {
        $cacheKey = 'dest_img_' . md5(strtolower(trim($placeName)));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($placeName) {
            return $this->resolveImage($placeName);
        });
    }

    /**
     * Batch-lookup — kembalikan map [nama_tempat => data_gambar].
     * Dipanggil dari DestinationController::getBatchImages().
     *
     * @param  string[] $places
     * @return array<string, array{url: string|null, source: string, credit: string|null}>
     */
    public function getBatchImages(array $places): array
    {
        $results = [];

        foreach ($places as $place) {
            $results[$place] = $this->getImageForPlace($place);
        }

        return $results;
    }

    // ── Resolution chain ──────────────────────────────────────────────────────

    private function resolveImage(string $placeName): array
    {
        // 1️⃣  Unsplash
        $unsplash = $this->fromUnsplash($placeName);
        if ($unsplash['url']) return $unsplash;

        // 2️⃣  Pexels
        $pexels = $this->fromPexels($placeName);
        if ($pexels['url']) return $pexels;

        // Tidak ada gambar → null (JS pakai picsum placeholder)
        return ['url' => null, 'source' => 'none', 'credit' => null];
    }

    // ── Source 1: Unsplash ────────────────────────────────────────────────────

    private function fromUnsplash(string $placeName): array
    {
        $accessKey = config('services.unsplash.access_key');
        if (!$accessKey) {
            return ['url' => null, 'source' => 'unsplash', 'credit' => null];
        }

        try {
            $response = Http::withHeaders(['Authorization' => "Client-ID {$accessKey}"])
                ->timeout(8)
                ->get('https://api.unsplash.com/search/photos', [
                    'query'          => $placeName . ' Indonesia tourism',
                    'per_page'       => 5,
                    'orientation'    => 'landscape',
                    'content_filter' => 'high',
                ]);

            if (!$response->successful()) {
                Log::debug('[ImageService] Unsplash non-200', [
                    'place'  => $placeName,
                    'status' => $response->status(),
                ]);
                return ['url' => null, 'source' => 'unsplash', 'credit' => null];
            }

            $results = $response->json('results', []);
            if (empty($results)) {
                return ['url' => null, 'source' => 'unsplash', 'credit' => null];
            }

            $photo  = $results[0];
            $rawUrl = $photo['urls']['raw'] ?? $photo['urls']['full'] ?? null;
            if (!$rawUrl) {
                return ['url' => null, 'source' => 'unsplash', 'credit' => null];
            }

            $url          = $rawUrl . '&w=' . self::UNSPLASH_WIDTH . '&h=' . self::UNSPLASH_HEIGHT . '&fit=crop&auto=format&q=80';
            $photographer = $photo['user']['name'] ?? 'Unknown';
            $photoLink    = $photo['links']['html'] ?? 'https://unsplash.com';

            return [
                'url'    => $url,
                'source' => 'unsplash',
                'credit' => "Photo by {$photographer} on Unsplash",
                'thumb'  => $photo['urls']['small'] ?? $url,
            ];
        } catch (\Throwable $e) {
            Log::warning('[ImageService] Unsplash error', ['place' => $placeName, 'err' => $e->getMessage()]);
        }

        return ['url' => null, 'source' => 'unsplash', 'credit' => null];
    }

    // ── Source 2: Pexels ─────────────────────────────────────────────────────

    private function fromPexels(string $placeName): array
    {
        $apiKey = config('services.pexels.api_key');
        if (!$apiKey) {
            return ['url' => null, 'source' => 'pexels', 'credit' => null];
        }

        try {
            $response = Http::withHeaders(['Authorization' => $apiKey])
                ->timeout(8)
                ->get('https://api.pexels.com/v1/search', [
                    'query'       => $placeName . ' Indonesia travel',
                    'per_page'    => self::PEXELS_PER_PAGE,
                    'orientation' => 'landscape',
                ]);

            if (!$response->successful()) {
                Log::debug('[ImageService] Pexels non-200', [
                    'place'  => $placeName,
                    'status' => $response->status(),
                ]);
                return ['url' => null, 'source' => 'pexels', 'credit' => null];
            }

            $photos = $response->json('photos', []);
            if (empty($photos)) {
                return ['url' => null, 'source' => 'pexels', 'credit' => null];
            }

            $photo = $photos[0];
            $src   = $photo['src'] ?? [];
            $url   = $src['large2x'] ?? $src['large'] ?? $src['original'] ?? null;

            if (!$url) {
                return ['url' => null, 'source' => 'pexels', 'credit' => null];
            }

            $photographer = $photo['photographer'] ?? 'Unknown';

            return [
                'url'    => $url,
                'source' => 'pexels',
                'credit' => "Photo by {$photographer} on Pexels",
                'thumb'  => $src['medium'] ?? $url,
            ];
        } catch (\Throwable $e) {
            Log::warning('[ImageService] Pexels error', ['place' => $placeName, 'err' => $e->getMessage()]);
        }

        return ['url' => null, 'source' => 'pexels', 'credit' => null];
    }
}
