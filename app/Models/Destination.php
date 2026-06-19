<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Destination extends Model
{
    protected $fillable = [
        'slug', 'name', 'province', 'city',
        'image_path', 'image_url', 'image_credit',
        'category', 'description', 'lat', 'lng',
        'is_active', 'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority'  => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForProvince(Builder $query, string $province): Builder
    {
        // Normalisasi: cari persis dulu, lalu LIKE
        return $query->where(function ($q) use ($province) {
            $q->where('province', $province)
              ->orWhere('province', 'LIKE', "%{$province}%");
        });
    }

    // ── Accessors ─────────────────────────────────────────────────────────

    /**
     * Kembalikan URL gambar yang siap dipakai.
     * Prioritas: image_path (lokal) → image_url (eksternal) → null
     */
    public function getImageSrcAttribute(): ?string
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return $this->image_url ?: null;
    }

    // ── Static helpers ────────────────────────────────────────────────────

    /**
     * Cari gambar untuk sebuah nama tempat.
     * Dipanggil dari DestinationImageService.
     */
    public static function findByPlaceName(string $placeName): ?self
    {
        $placeName = trim($placeName);

        // 1. Match nama persis (case-insensitive)
        $found = static::active()
            ->whereRaw('LOWER(name) = ?', [strtolower($placeName)])
            ->orderBy('priority')
            ->first();

        if ($found) return $found;

        // 2. Match kata kunci utama (nama tempat di-tokenize)
        $words = array_filter(explode(' ', strtolower($placeName)), fn($w) => strlen($w) > 3);
        foreach ($words as $word) {
            $found = static::active()
                ->whereRaw('LOWER(name) LIKE ?', ["%{$word}%"])
                ->orderBy('priority')
                ->first();
            if ($found) return $found;
        }

        return null;
    }

    /**
     * Ambil semua destinasi dalam satu provinsi, diurutkan prioritas.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function forProvince(string $province)
    {
        return static::active()
            ->forProvince($province)
            ->orderBy('priority')
            ->get();
    }
}
