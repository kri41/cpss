<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Talenta extends Model
{
    use HasFactory;

    protected $table = 'talenta';

    protected $fillable = [
        'user_id',
        'nama_atlet',
        'cabang_olahraga',
        'asal_sekolah_atau_klub',
        'prestasi_tertinggi',
        'status_pembinaan',
    ];

    /**
     * Relasi ke User (Admin yang input)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan cabang olahraga
     */
    public function scopeCabangOlahraga($query, $cabang)
    {
        return $query->where('cabang_olahraga', $cabang);
    }

    /**
     * Scope untuk filter berdasarkan status pembinaan
     */
    public function scopeStatusPembinaan($query, $status)
    {
        return $query->where('status_pembinaan', $status);
    }

    /**
     * Scope untuk filter atlet yang aktif di PPLP
     */
    public function scopeAktifPplp($query)
    {
        return $query->where('status_pembinaan', 'Aktif PPLP');
    }
}
