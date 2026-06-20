<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    use HasFactory;

    protected $table = 'point_transactions';

    protected $fillable = [
        'user_id',
        'related_type',
        'related_id',
        'jenis_aksi',
        'poin',
        'status',
        'dibatalkan_oleh',
        'alasan_pembatalan',
    ];

    protected $casts = [
        'poin' => 'integer',
    ];

    /**
     * Relasi ke User (relawan penerima poin)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke User (admin pembatal)
     */
    public function dibatalkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh');
    }

    /**
     * Scope transaksi valid
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'valid');
    }

    /**
     * Total poin valid per user
     */
    public static function totalPoinUser(int $userId): int
    {
        return self::where('user_id', $userId)->where('status', 'valid')->sum('poin');
    }
}
