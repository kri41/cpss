<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kehadiran extends Model
{
    use HasFactory;

    protected $table = 'kehadiran';

    protected $fillable = [
        'partisipasi_id',
        'nama_peserta',
        'jenis_olahraga',
        'rpe',
        'jenis_kelamin',
        'usia',
        'kelompok_usia',
        'kategori_khusus',
        'status',
        'catatan',
        'created_by',
        'sumber',
    ];

    protected $casts = [
        'usia' => 'integer',
    ];

    /**
     * Relasi ke Partisipasi
     */
    public function partisipasi(): BelongsTo
    {
        return $this->belongsTo(Partisipasi::class);
    }

    /**
     * Relasi ke User (pencatat)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
