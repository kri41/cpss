<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'desa',
        'kecamatan',
        'kabupaten',
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
     * Scope untuk filter berdasarkan role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
