<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan setiap kali sebuah laporan dihasilkan / diunduh dari sistem.
 * Menandai "waktu selesai membuat laporan" pada alur kerja versi aplikasi.
 */
class PembuatanLaporan extends Model
{
    protected $table = 'pembuatan_laporan';

    protected $fillable = [
        'user_id', 'jenis', 'subjek_user_id', 'format', 'render_ms', 'dibuat_at',
    ];

    protected $casts = [
        'dibuat_at' => 'datetime',
        'render_ms' => 'integer',
    ];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subjek(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subjek_user_id');
    }
}
