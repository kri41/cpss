<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'provinsi',
        'desa',
        'kecamatan',
        'kabupaten',
        'google_id',
        'avatar',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke User Notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class)->latest();
    }

    /**
     * Relasi ke Prasarana
     */
    public function prasarana(): HasMany
    {
        return $this->hasMany(Prasarana::class);
    }

    /**
     * Relasi ke Partisipasi
     */
    public function partisipasi(): HasMany
    {
        return $this->hasMany(Partisipasi::class);
    }

    /**
     * Relasi ke Events
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Relasi ke Talenta
     */
    public function talenta(): HasMany
    {
        return $this->hasMany(Talenta::class);
    }

    /**
     * Relasi ke Tenaga Ahli
     */
    public function tenagaAhli(): HasMany
    {
        return $this->hasMany(TenagaAhli::class);
    }

    /**
     * Relasi ke Audit Logs
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Cek apakah user adalah Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Cek apakah user adalah Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    /**
     * Cek apakah user adalah Relawan
     */
    public function isRelawan(): bool
    {
        return $this->role === 'relawan';
    }

    /**
     * Relasi ke Point Transactions
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Relasi ke Badges (lencana)
     */
    public function badges(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    /**
     * Relasi ke Kehadiran (yang dicatat oleh user ini)
     */
    public function kehadiranDibuat(): HasMany
    {
        return $this->hasMany(Kehadiran::class, 'created_by');
    }

    /**
     * Cek apakah user bisa mengedit model (data ownership + wilayah + status validasi)
     */
    public function canEdit($model): bool
    {
        // Super admin bisa edit apapun
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Data yang sudah divalidasi tidak bisa diedit (kecuali super admin)
        if ($model->status_validasi === 'validated') {
            return false;
        }

        // Pemilik data bisa edit
        if ($model->user_id === $this->id) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah user bisa memvalidasi model (admin wilayah / super admin)
     */
    public function canValidate($model): bool
    {
        // Super admin bisa validasi apapun
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Hanya admin yang bisa validasi
        if (!$this->isAdmin() || $this->role === 'relawan') {
            return false;
        }

        // Admin hanya bisa validasi data di wilayah yang sama
        return $this->isSameWilayah($model);
    }

    /**
     * Cek apakah user berada di wilayah yang sama dengan model
     */
    public function isSameWilayah($model): bool
    {
        // Cocokkan kabupaten
        if (!empty($this->kabupaten) && !empty($model->kabupaten)) {
            if (strtolower(trim($this->kabupaten)) !== strtolower(trim($model->kabupaten))) {
                return false;
            }
        }

        // Cocokkan kecamatan (jika keduanya ada)
        if (!empty($this->kecamatan) && !empty($model->kecamatan)) {
            if (strtolower(trim($this->kecamatan)) !== strtolower(trim($model->kecamatan))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Batasi query ke wilayah (kabupaten/kecamatan) milik user ini —
     * dipakai agar relawan di dashboard hanya melihat data di daerahnya sendiri.
     */
    public function scopeToOwnWilayah($query)
    {
        if (!empty($this->kabupaten)) {
            $query->whereRaw('LOWER(TRIM(kabupaten)) = ?', [strtolower(trim($this->kabupaten))]);
        }
        if (!empty($this->kecamatan)) {
            $query->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($this->kecamatan))]);
        }

        return $query;
    }
}
