<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KampungOlahraga extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'kampung_olahraga';

    protected $fillable = [
        'user_id', 'nama_kampung', 'alamat',
        'provinsi', 'kabupaten', 'kecamatan', 'desa', 'rt', 'rw',
        'status_validasi', 'qr_token', 'catatan_admin',
        'latitude', 'longitude',
    ];

    protected $casts = [
        'latitude'  => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(CheckinKampung::class);
    }

    public function fasil(): HasMany
    {
        return $this->hasMany(Prasarana::class, 'kampung_olahraga_id');
    }

    public function klubKomunitas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'kampung_klub', 'kampung_olahraga_id', 'club_id');
    }

    public static function generateQrToken(): string
    {
        do {
            $token = Str::random(16);
        } while (static::where('qr_token', $token)->exists());

        return $token;
    }

    public function totalCheckin(): int
    {
        return $this->checkins()->count();
    }

    public function skorPoin(): int
    {
        $total = $this->totalCheckin();
        $poin  = 0;

        KomponenSyarat::where('aktif', true)->each(function ($k) use ($total, &$poin) {
            if ($total >= $k->target_checkin) {
                $poin += $k->poin;
            }
        });

        return $poin;
    }

    public function scopeValidated($query)
    {
        return $query->where('status_validasi', 'validated');
    }

    public function getRtRwLabelAttribute(): ?string
    {
        if (!$this->rt && !$this->rw) {
            return null;
        }

        return 'RT ' . ($this->rt ?: '-') . ' / RW ' . ($this->rw ?: '-');
    }
}
