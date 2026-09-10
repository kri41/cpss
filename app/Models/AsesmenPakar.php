<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hasil transkripsi lembar audit asesor pakar untuk satu prasarana.
 */
class AsesmenPakar extends Model
{
    protected $table = 'asesmen_pakar';

    protected $fillable = [
        'prasarana_id', 'dicatat_oleh', 'nama_asesor', 'tanggal_asesmen',
        'waktu_mulai', 'waktu_selesai', 'durasi_laporan_detik',
        'kondisi_lantai', 'kondisi_ring', 'kondisi_net', 'kondisi_gawang',
        'kondisi_lapangan', 'kondisi_ventilasi', 'kondisi_pencahayaan', 'kondisi_kamar_mandi',
        'akses_disabilitas', 'akses_parkir', 'akses_transportasi',
        'fasilitas_ruang_ganti', 'fasilitas_tribun',
        'kategori_olahraga_ids', 'catatan',
    ];

    protected $casts = [
        'tanggal_asesmen' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'durasi_laporan_detik' => 'integer',
        'akses_disabilitas' => 'boolean',
        'akses_parkir' => 'boolean',
        'akses_transportasi' => 'boolean',
        'fasilitas_ruang_ganti' => 'boolean',
        'fasilitas_tribun' => 'boolean',
        'kategori_olahraga_ids' => 'array',
    ];

    /** Kolom kondisi (1-5) yang dibandingkan dengan versi relawan. */
    public const KONDISI = [
        'kondisi_lantai' => 'Lantai',
        'kondisi_ring' => 'Ring',
        'kondisi_net' => 'Net',
        'kondisi_gawang' => 'Gawang',
        'kondisi_lapangan' => 'Lapangan',
        'kondisi_ventilasi' => 'Ventilasi',
        'kondisi_pencahayaan' => 'Pencahayaan',
        'kondisi_kamar_mandi' => 'Kamar Mandi',
    ];

    /** Kolom aksesibilitas/kelengkapan (boolean) yang dibandingkan. */
    public const AKSES = [
        'akses_disabilitas' => 'Akses Disabilitas',
        'akses_parkir' => 'Akses Parkir',
        'akses_transportasi' => 'Akses Transportasi',
        'fasilitas_ruang_ganti' => 'Ruang Ganti',
        'fasilitas_tribun' => 'Tribun',
    ];

    public function prasarana(): BelongsTo
    {
        return $this->belongsTo(Prasarana::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    /** Durasi asesmen lapangan (detik) = waktu_selesai − waktu_mulai. */
    public function getDurasiAsesmenDetikAttribute(): ?int
    {
        if (! $this->waktu_mulai || ! $this->waktu_selesai) {
            return null;
        }

        return (int) round(abs($this->waktu_mulai->diffInSeconds($this->waktu_selesai, false)));
    }
}
