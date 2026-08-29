<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Penyimpanan konfigurasi aplikasi yang bisa diubah lewat menu Pengaturan
 * (mis. server email SMTP, isi email verifikasi). Key-value sederhana + cache.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /** Key yang disimpan dalam bentuk terenkripsi. */
    public const ENCRYPTED = ['mail_password'];

    /** Nilai bawaan bila key belum pernah disimpan. */
    public const DEFAULTS = [
        'mail_mailer' => 'log',
        'mail_host' => '',
        'mail_port' => '465',
        'mail_username' => '',
        'mail_password' => '',
        'mail_encryption' => 'ssl',   // ssl | tls | none
        'mail_from_address' => '',
        'mail_from_name' => 'Dataraga',

        'verify_email_subject' => 'Verifikasi Alamat Email Anda',
        'verify_email_greeting' => 'Halo {nama},',
        'verify_email_body' => 'Terima kasih telah mendaftar di {app}. Silakan klik tombol di bawah untuk memverifikasi alamat email Anda dan mengaktifkan akun.',
        'verify_email_button' => 'Verifikasi Email',
        'verify_email_outro' => 'Jika Anda tidak merasa membuat akun ini, abaikan saja email ini.',

        'welcome_email_enabled' => '1',
        'welcome_email_subject' => 'Akun Dataraga Anda Sudah Aktif',
        'welcome_email_greeting' => 'Halo {nama},',
        'welcome_email_body' => 'Alamat email Anda berhasil diverifikasi dan akun Anda kini aktif. Anda sudah dapat masuk ke {app} dan mulai berkontribusi.',
        'welcome_email_button' => 'Masuk ke Dataraga',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    /** Seluruh setting sebagai array key => value mentah (nilai tersimpan apa adanya). */
    public static function raw(): array
    {
        return Cache::rememberForever('app_settings', function () {
            try {
                return static::query()->pluck('value', 'key')->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /** Ambil satu nilai (otomatis dekripsi bila perlu), fallback ke DEFAULTS lalu $default. */
    public static function get(string $key, $default = null)
    {
        $raw = static::raw()[$key] ?? null;

        if ($raw === null || $raw === '') {
            return $default ?? (self::DEFAULTS[$key] ?? null);
        }

        if (in_array($key, self::ENCRYPTED, true)) {
            try {
                return Crypt::decryptString($raw);
            } catch (\Throwable) {
                return $default ?? (self::DEFAULTS[$key] ?? null);
            }
        }

        return $raw;
    }

    public static function put(string $key, $value): void
    {
        if (in_array($key, self::ENCRYPTED, true) && $value !== null && $value !== '') {
            $value = Crypt::encryptString($value);
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function putMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::put($key, $value);
        }
    }

    public static function flushCache(): void
    {
        Cache::forget('app_settings');
    }
}
