<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenagaAhli extends Model
{
    use HasFactory;

    protected $table = 'tenaga_ahli';

    protected $fillable = [
        'user_id',
        'nama_tenaga_ahli',
        'profesi',
        'nomor_sertifikat',
        'tingkat_lisensi',
    ];

    /**
     * Relasi ke User (Admin yang input)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan profesi
     */
    public function scopeProfesi($query, $profesi)
    {
        return $query->where('profesi', $profesi);
    }

    /**
     * Scope untuk filter berdasarkan tingkat lisensi
     */
    public function scopeTingkatLisensi($query, $tingkat)
    {
        return $query->where('tingkat_lisensi', $tingkat);
    }

    /**
     * Scope untuk tenaga ahli yang bersertifikat
     */
    public function scopeBersertifikat($query)
    {
        return $query->whereNotNull('nomor_sertifikat');
    }

    /**
     * Accessor untuk cek apakah memiliki sertifikat
     */
    public function getBersertifikatAttribute(): bool
    {
        return !is_null($this->nomor_sertifikat);
    }
}
