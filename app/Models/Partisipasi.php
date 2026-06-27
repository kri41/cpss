<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partisipasi extends Model
{
    use HasFactory;

    protected $table = 'partisipasi';

    protected $fillable = [
        'user_id',
        'qr_token',
        'lokasi_observasi',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'tanggal_observasi',
        'estimasi_jumlah_orang',
        'mayoritas_usia',
        'status_validasi',
        'komentar_validasi',
    ];

    protected $casts = [
        'tanggal_observasi' => 'date',
        'estimasi_jumlah_orang' => 'integer',
    ];

    /**
     * Relasi ke User (Relawan yang mencatat)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal
     */
    public function scopePeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_observasi', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan kelompok usia
     */
    public function scopeKelompokUsia($query, $kelompok)
    {
        return $query->where('mayoritas_usia', $kelompok);
    }

    /**
     * Relasi ke Kehadiran (peserta individu)
     */
    public function kehadiran(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    /**
     * Accessor untuk mendapatkan total partisipasi
     */
    public static function totalPartisipasi(): int
    {
        return self::sum('estimasi_jumlah_orang');
    }

    /**
     * Generate unique QR token
     */
    public static function generateQrToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while (self::where('qr_token', $token)->exists());

        return $token;
    }
}
