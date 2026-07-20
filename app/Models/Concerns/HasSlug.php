<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Memberi model kode unik (slug) sebagai route key, menggantikan id auto-increment
 * pada URL publik — mencegah enumerasi ("/prasarana/1", "/prasarana/2", dst) dan
 * tebak-tebakan ID di alamat yang dapat diakses publik.
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Kode unik acak (10 karakter, huruf kecil + angka) — tanpa embed nama,
     * agar URL tidak membocorkan data dan tetap pendek/konsisten di semua model.
     */
    public function generateUniqueSlug(): string
    {
        do {
            $candidate = Str::lower(Str::random(10));
        } while (static::where('slug', $candidate)->exists());

        return $candidate;
    }
}
