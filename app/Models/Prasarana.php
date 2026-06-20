<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prasarana extends Model
{
    use HasFactory;

    protected $table = 'prasarana';

    protected $fillable = [
        'user_id',
        'nama_fasilitas',
        'club_komunitas',
        'kategori_olahraga',
        'latitude',
        'longitude',
        'alamat',
        'desa',
        'kecamatan',
        'kabupaten',
        // Kondisi (1-5 scale)
        'kondisi_lantai',
        'kondisi_ring',
        'kondisi_net',
        'kondisi_gawang',
        'kondisi_lapangan',
        'kondisi_ventilasi',
        'kondisi_pencahayaan',
        'kondisi_kamar_mandi',
        // Akses & Fasilitas
        'akses_disabilitas',
        'akses_parkir',
        'akses_transportasi',
        'fasilitas_ruang_ganti',
        'fasilitas_tribun',
        // Foto
        'foto_path',
        'foto_tambahan',
        'keterangan',
        'status_validasi',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'akses_disabilitas' => 'boolean',
        'akses_parkir' => 'boolean',
        'akses_transportasi' => 'boolean',
        'fasilitas_ruang_ganti' => 'boolean',
        'fasilitas_tribun' => 'boolean',
        'foto_tambahan' => 'array',
        'kondisi_lantai' => 'integer',
        'kondisi_ring' => 'integer',
        'kondisi_net' => 'integer',
        'kondisi_gawang' => 'integer',
        'kondisi_lapangan' => 'integer',
        'kondisi_ventilasi' => 'integer',
        'kondisi_pencahayaan' => 'integer',
        'kondisi_kamar_mandi' => 'integer',
    ];

    /**
     * Rating labels
     */
    const RATING_LABELS = [
        1 => 'Buruk Sekali',
        2 => 'Buruk',
        3 => 'Cukup',
        4 => 'Baik',
        5 => 'Baik Sekali',
    ];

    const RATING_COLORS = [
        1 => 'red',
        2 => 'orange',
        3 => 'yellow',
        4 => 'blue',
        5 => 'green',
    ];

    /**
     * Relasi ke User (Relawan yang melaporkan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Clubs yang menggunakan prasarana ini
     */
    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    /**
     * Get rating label
     */
    public function getRatingLabel($rating): string
    {
        return self::RATING_LABELS[$rating] ?? 'Tidak Diketahui';
    }

    /**
     * Get rating color class
     */
    public function getRatingColor($rating): string
    {
        $colors = [
            1 => 'bg-red-100 text-red-800',
            2 => 'bg-orange-100 text-orange-800',
            3 => 'bg-yellow-100 text-yellow-800',
            4 => 'bg-blue-100 text-blue-800',
            5 => 'bg-green-100 text-green-800',
        ];
        return $colors[$rating] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get average condition score
     */
    public function getAverageKondisiAttribute(): float
    {
        $kondisi = [
            $this->kondisi_lantai,
            $this->kondisi_ring,
            $this->kondisi_net,
            $this->kondisi_gawang,
            $this->kondisi_lapangan,
            $this->kondisi_ventilasi,
            $this->kondisi_pencahayaan,
            $this->kondisi_kamar_mandi,
        ];
        
        $validKondisi = array_filter($kondisi, fn($v) => $v !== null);
        
        if (empty($validKondisi)) {
            return 0;
        }
        
        return round(array_sum($validKondisi) / count($validKondisi), 1);
    }

    /**
     * Get overall status
     */
    public function getStatusAttribute(): string
    {
        $avg = $this->average_kondisi;
        
        if ($avg >= 4) return 'Sangat Baik';
        if ($avg >= 3) return 'Baik';
        if ($avg >= 2) return 'Cukup';
        return 'Perlu Perbaikan';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        $avg = $this->average_kondisi;
        
        if ($avg >= 4) return 'bg-green-100 text-green-800';
        if ($avg >= 3) return 'bg-blue-100 text-blue-800';
        if ($avg >= 2) return 'bg-yellow-100 text-yellow-800';
        return 'bg-red-100 text-red-800';
    }

    /**
     * Scope untuk filter berdasarkan kondisi lantai
     */
    public function scopeKondisi($query, $kondisi)
    {
        return $query->where('kondisi_lantai', $kondisi);
    }

    /**
     * Scope untuk filter yang memiliki akses disabilitas
     */
    public function scopeRamahDisabilitas($query)
    {
        return $query->where('akses_disabilitas', true);
    }

    /**
     * Scope untuk filter berdasarkan club/komunitas
     */
    public function scopeClubKomunitas($query, $club)
    {
        return $query->where('club_komunitas', 'like', '%' . $club . '%');
    }

    /**
     * Scope untuk filter berdasarkan range rating
     */
    public function scopeRatingRange($query, $min, $max)
    {
        return $query->where(function($q) use ($min, $max) {
            $q->whereBetween('kondisi_lantai', [$min, $max])
              ->orWhereBetween('kondisi_lapangan', [$min, $max]);
        });
    }
}
