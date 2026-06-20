<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prasarana_id',
        'nama_club',
        'deskripsi',
        'ketua_club',
        'narahubung',
        'no_telepon',
        'email',
        'alamat',
        'desa',
        'kecamatan',
        'kabupaten',
        'logo_path',
        'aktif',
        'tanggal_berdiri',
        'status_validasi',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'tanggal_berdiri' => 'date',
    ];

    /**
     * Relasi ke User (pemilik/relawan yang mendaftarkan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Prasarana (tempat latihan)
     */
    public function prasarana(): BelongsTo
    {
        return $this->belongsTo(Prasarana::class);
    }

    /**
     * Relasi ke Jadwal Latihan
     */
    public function jadwalLatihan(): HasMany
    {
        return $this->hasMany(JadwalLatihan::class);
    }

    /**
     * Scope untuk club yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope untuk filter berdasarkan prasarana
     */
    public function scopeByPrasarana($query, $prasaranaId)
    {
        return $query->where('prasarana_id', $prasaranaId);
    }

    /**
     * Get jadwal hari ini
     */
    public function jadwalHariIni()
    {
        return $this->jadwalLatihan()
            ->where('hari', now()->locale('id')->dayName)
            ->where('aktif', true)
            ->get();
    }

    /**
     * Get umur club dalam tahun
     */
    public function getUmurAttribute()
    {
        if (!$this->tanggal_berdiri) {
            return null;
        }
        return $this->tanggal_berdiri->diffInYears(now());
    }
}
