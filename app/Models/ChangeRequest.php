<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'changeable_type', 'changeable_id',
        'perubahan', 'alasan', 'status', 'catatan_admin',
        'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'perubahan' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function changeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Nama field yang bisa dibaca manusia, untuk ditampilkan di halaman tinjau.
     */
    public function fieldLabels(): array
    {
        return match ($this->changeable_type) {
            'prasarana' => [
                'nama_fasilitas' => 'Nama Fasilitas', 'club_komunitas' => 'Klub/Komunitas',
                'kategori_olahraga' => 'Kategori Olahraga', 'alamat' => 'Alamat',
                'desa' => 'Desa', 'kecamatan' => 'Kecamatan', 'kabupaten' => 'Kabupaten',
                'kondisi_lantai' => 'Kondisi Lantai', 'kondisi_ring' => 'Kondisi Ring',
                'kondisi_net' => 'Kondisi Net', 'kondisi_gawang' => 'Kondisi Gawang',
                'kondisi_lapangan' => 'Kondisi Lapangan', 'kondisi_ventilasi' => 'Kondisi Ventilasi',
                'kondisi_pencahayaan' => 'Kondisi Pencahayaan', 'kondisi_kamar_mandi' => 'Kondisi Kamar Mandi',
                'akses_disabilitas' => 'Akses Disabilitas', 'akses_parkir' => 'Akses Parkir',
                'akses_transportasi' => 'Akses Transportasi', 'fasilitas_ruang_ganti' => 'Ruang Ganti',
                'fasilitas_tribun' => 'Tribun', 'keterangan' => 'Keterangan',
            ],
            'club' => [
                'nama_club' => 'Nama Klub/Komunitas', 'deskripsi' => 'Deskripsi',
                'ketua_club' => 'Ketua', 'narahubung' => 'Narahubung', 'no_telepon' => 'No. Telepon',
                'email' => 'Email', 'alamat' => 'Alamat', 'desa' => 'Desa',
                'kecamatan' => 'Kecamatan', 'kabupaten' => 'Kabupaten',
                'tanggal_berdiri' => 'Tanggal Berdiri', 'aktif' => 'Status Aktif',
                'jenis_olahraga_id' => 'Jenis Olahraga', 'prasarana_id' => 'Prasarana',
            ],
            'event' => [
                'nama_event' => 'Nama Event', 'tingkat' => 'Tingkat',
                'tanggal_mulai' => 'Tanggal Mulai', 'tanggal_selesai' => 'Tanggal Selesai',
                'deskripsi_kegiatan' => 'Deskripsi Kegiatan', 'desa' => 'Desa',
                'kecamatan' => 'Kecamatan', 'kabupaten' => 'Kabupaten', 'provinsi' => 'Provinsi',
            ],
            default => [],
        };
    }

    public function typeLabel(): string
    {
        return match ($this->changeable_type) {
            'prasarana' => 'Prasarana',
            'club' => 'Klub/Komunitas',
            'event' => 'Event',
            default => ucfirst($this->changeable_type),
        };
    }
}
