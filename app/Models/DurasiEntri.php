<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lama pengisian satu formulir pelaporan (mulai input → tekan Simpan).
 * Dipakai untuk pembandingan alur kerja konvensional vs aplikasi (disertasi).
 */
class DurasiEntri extends Model
{
    protected $table = 'durasi_entri';

    protected $fillable = [
        'entri_type', 'entri_id', 'user_id',
        'mulai_input_at', 'selesai_input_at', 'durasi_detik', 'sumber',
    ];

    protected $casts = [
        'mulai_input_at' => 'datetime',
        'selesai_input_at' => 'datetime',
        'durasi_detik' => 'integer',
    ];

    /** Perkiraan waktu pengisian per modul (detik) — dasar untuk data lama. */
    public const ESTIMASI_MEDIAN = [
        'prasarana' => 300,  // ± 5 menit  (25+ field: kondisi, akses, foto, GPS, kategori)
        'clubs' => 210,  // ± 3,5 menit (kontak lengkap + logo + tanggal berdiri)
        'kampung_olahraga' => 180,  // ± 3 menit   (alamat + RT/RW + wilayah + GPS)
        'events' => 150,  // ± 2,5 menit (nama, tingkat, 2 tanggal, deskripsi, flyer)
        'partisipasi' => 90,   // ± 1,5 menit (lokasi, tanggal, estimasi jumlah, usia)
    ];

    public const LABEL = [
        'prasarana' => 'Prasarana',
        'clubs' => 'Klub/Komunitas',
        'events' => 'Event',
        'partisipasi' => 'Partisipasi',
        'kampung_olahraga' => 'Kampung Olahraga',
    ];

    public const MODEL = [
        'prasarana' => Prasarana::class,
        'clubs' => Club::class,
        'events' => Event::class,
        'partisipasi' => Partisipasi::class,
        'kampung_olahraga' => KampungOlahraga::class,
    ];

    public const KOLOM_NAMA = [
        'prasarana' => 'nama_fasilitas',
        'clubs' => 'nama_club',
        'events' => 'nama_event',
        'partisipasi' => 'lokasi_observasi',
        'kampung_olahraga' => 'nama_kampung',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDurasiMenitAttribute(): float
    {
        return round($this->durasi_detik / 60, 2);
    }
}
