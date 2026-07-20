<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'user_id',
        'prasarana_id',
        'jenis_olahraga_id',
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
        'komentar_validasi',
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
     * Relasi ke Jenis Olahraga (untuk auto-fill saat check-in)
     */
    public function jenisOlahraga(): BelongsTo
    {
        return $this->belongsTo(JenisOlahraga::class);
    }

    /**
     * Relasi ke Kampung Olahraga (rumah-rumah tempat klub/komunitas ini terdaftar)
     */
    public function kampungOlahraga(): BelongsToMany
    {
        return $this->belongsToMany(KampungOlahraga::class, 'kampung_klub', 'club_id', 'kampung_olahraga_id');
    }

    /**
     * Relasi ke check-in QR peserta yang memilih klub/komunitas ini
     */
    public function checkins(): HasMany
    {
        return $this->hasMany(CheckinKampung::class, 'club_id');
    }

    /**
     * Scope: klub/komunitas yang berada di wilayah yang sama dengan Kampung Olahraga
     */
    public function scopeSameWilayahAs($query, KampungOlahraga $kampung)
    {
        return $query
            ->when($kampung->kabupaten, fn($q) => $q->whereRaw('LOWER(TRIM(kabupaten)) = ?', [strtolower(trim($kampung->kabupaten))]))
            ->when($kampung->kecamatan, fn($q) => $q->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kampung->kecamatan))]))
            ->when($kampung->desa, fn($q) => $q->whereRaw('LOWER(TRIM(desa)) = ?', [strtolower(trim($kampung->desa))]));
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
     * Scope untuk data yang sudah divalidasi
     */
    public function scopeValidated($query)
    {
        return $query->where('status_validasi', 'validated');
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
