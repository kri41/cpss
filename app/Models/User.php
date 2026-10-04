<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasSlug, Notifiable;

    /**
     * Ambang poin tervalidasi minimal sebelum relawan bisa membuka/membagikan
     * Link Live-nya sendiri dari dashboard (mis. untuk bukti aktualisasi).
     * Admin tidak terikat batas ini — menu Laporan Relawan selalu bisa akses.
     */
    public const MIN_POIN_LIVE_REPORT = 200;

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
        'public_report_token',
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

    protected static function booted(): void
    {
        // Relawan baru langsung dapat token link Live Report.
        // Role default aplikasi = 'relawan', jadi user tanpa role eksplisit
        // (mis. pendaftar baru) tetap ikut dapat token.
        static::creating(function (User $user) {
            if (($user->role ?? 'relawan') === 'relawan' && empty($user->public_report_token)) {
                try {
                    $user->public_report_token = static::generateReportToken();
                } catch (\Throwable) {
                    // kolom belum ada (migrasi belum dijalankan) — lewati saja
                }
            }
        });
    }

    /**
     * Kirim email verifikasi dengan template yang bisa diubah di menu Pengaturan.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    /**
     * Kirim email atur ulang kata sandi dengan isi Bahasa Indonesia.
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /* ================= LIVE REPORT (link publik) ================= */

    public static function generateReportToken(): string
    {
        do {
            $token = Str::random(48);
        } while (static::where('public_report_token', $token)->exists());

        return $token;
    }

    /** Pastikan relawan punya token (untuk data lama yang role-nya baru diubah). */
    public function ensurePublicReportToken(): ?string
    {
        if (! empty($this->public_report_token)) {
            return $this->public_report_token;
        }

        try {
            $this->forceFill(['public_report_token' => static::generateReportToken()])->save();

            return $this->public_report_token;
        } catch (\Throwable $e) {
            report($e); // mis. kolom belum ada — jangan sampai menggagalkan halaman

            return null;
        }
    }

    public function regeneratePublicReportToken(): string
    {
        $this->forceFill(['public_report_token' => static::generateReportToken()])->save();

        return $this->public_report_token;
    }

    public function publicReportUrl(): ?string
    {
        return $this->public_report_token ? url('/r/'.$this->public_report_token) : null;
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
     * Relasi ke Klub/Komunitas yang didaftarkan
     */
    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    /**
     * Relasi ke Kampung Olahraga yang didaftarkan
     */
    public function kampungOlahraga(): HasMany
    {
        return $this->hasMany(KampungOlahraga::class);
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
    public function badges(): BelongsToMany
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
        if (! $this->isAdmin() || $this->role === 'relawan') {
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
        if (! empty($this->kabupaten) && ! empty($model->kabupaten)) {
            if (strtolower(trim($this->kabupaten)) !== strtolower(trim($model->kabupaten))) {
                return false;
            }
        }

        // Cocokkan kecamatan (jika keduanya ada)
        if (! empty($this->kecamatan) && ! empty($model->kecamatan)) {
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
        if (! empty($this->kabupaten)) {
            $query->whereRaw('LOWER(TRIM(kabupaten)) = ?', [strtolower(trim($this->kabupaten))]);
        }
        if (! empty($this->kecamatan)) {
            $query->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($this->kecamatan))]);
        }

        return $query;
    }
}
