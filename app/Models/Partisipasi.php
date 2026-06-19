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
        'lokasi_observasi',
        'tanggal_observasi',
        'estimasi_jumlah_orang',
        'mayoritas_usia',
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
     * Accessor untuk mendapatkan total partisipasi
     */
    public static function totalPartisipasi(): int
    {
        return self::sum('estimasi_jumlah_orang');
    }
}
