<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalLatihan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_latihan';

    protected $fillable = [
        'club_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'aktif',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke Club
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Scope untuk jadwal yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope untuk filter berdasarkan hari
     */
    public function scopeHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Get durasi latihan dalam menit
     */
    public function getDurasiMenitAttribute()
    {
        if (!$this->jam_mulai || !$this->jam_selesai) {
            return 0;
        }
        return $this->jam_mulai->diffInMinutes($this->jam_selesai);
    }

    /**
     * Get format waktu lengkap
     */
    public function getWaktuFormatAttribute()
    {
        return $this->jam_mulai->format('H:i') . ' - ' . $this->jam_selesai->format('H:i');
    }
}
