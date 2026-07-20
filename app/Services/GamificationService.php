<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserNotification;
use Carbon\Carbon;

class GamificationService
{
    /**
     * Aturan poin default
     */
    const RULES = [
        'prasarana_baru'   => ['poin' => 50, 'batas' => '1x_per_entitas'],
        'prasarana_update' => ['poin' => 15, 'batas' => '1x_per_entitas_30hari'],
        'club_baru'        => ['poin' => 40, 'batas' => '1x_per_entitas'],
        'club_update'      => ['poin' => 10, 'batas' => '1x_per_entitas'],
        'event_baru'       => ['poin' => 20, 'batas' => 'tidak_dibatasi'],
        'partisipasi_valid'=> ['poin' =>  3, 'batas' => '1x_per_lokasi_tanggal'],
        'kampung_baru'     => ['poin' => 30, 'batas' => '1x_per_entitas'],
    ];

    /**
     * Kredit poin setelah validasi minimum lolos
     */
    public static function awardPoints(int $userId, string $kodeAktivitas, string $relatedType, int $relatedId, array $meta = []): ?PointTransaction
    {
        $rule = self::RULES[$kodeAktivitas] ?? null;
        if (!$rule) {
            return null;
        }

        // Cek pembatasan pengulangan
        if (!self::cekBatas($userId, $kodeAktivitas, $relatedType, $relatedId, $meta)) {
            return null;
        }

        $tx = PointTransaction::create([
            'user_id'       => $userId,
            'related_type'  => $relatedType,
            'related_id'    => $relatedId,
            'jenis_aksi'    => str_contains($kodeAktivitas, '_update') ? 'update' : 'baru',
            'poin'          => $rule['poin'],
            'status'        => 'valid',
        ]);

        // Update denormalisasi total_poin pada users
        self::updateTotalPoin($userId);

        // Cek dan berikan lencana
        self::cekDanBerikanLencana($userId);

        return $tx;
    }

    /**
     * Tentukan kode aktivitas saat validasi (baru vs update)
     */
    public static function resolveKodeAktivitas(string $baseCode, int $userId, string $relatedType, int $relatedId): string
    {
        // Cek apakah sudah pernah ada transaksi 'baru' yang valid untuk entitas ini
        $hasBaru = PointTransaction::where('user_id', $userId)
            ->where('related_type', $relatedType)
            ->where('related_id', $relatedId)
            ->where('jenis_aksi', 'baru')
            ->where('status', 'valid')
            ->exists();

        if ($hasBaru && str_contains($baseCode, '_baru')) {
            // Ganti _baru menjadi _update
            return str_replace('_baru', '_update', $baseCode);
        }

        return $baseCode;
    }

    /**
     * Batalkan poin (oleh admin)
     */
    public static function batalkanPoin(int $transactionId, int $adminId, string $alasan): bool
    {
        $tx = PointTransaction::find($transactionId);
        if (!$tx) return false;

        $tx->update([
            'status'            => 'dibatalkan',
            'dibatalkan_oleh'   => $adminId,
            'alasan_pembatalan' => $alasan,
        ]);

        self::updateTotalPoin($tx->user_id);

        return true;
    }

    /**
     * Cek pembatasan pengulangan
     */
    protected static function cekBatas(int $userId, string $kode, string $relatedType, int $relatedId, array $meta): bool
    {
        $rule = self::RULES[$kode]['batas'];

        if ($rule === 'tidak_dibatasi') {
            return true;
        }

        if ($rule === '1x_per_entitas') {
            $exists = PointTransaction::where('user_id', $userId)
                ->where('related_type', $relatedType)
                ->where('related_id', $relatedId)
                ->where('jenis_aksi', str_contains($kode, '_update') ? 'update' : 'baru')
                ->where('status', 'valid')
                ->exists();
            return !$exists;
        }

        if ($rule === '1x_per_entitas_30hari') {
            $last = PointTransaction::where('user_id', $userId)
                ->where('related_type', $relatedType)
                ->where('related_id', $relatedId)
                ->where('jenis_aksi', 'update')
                ->where('status', 'valid')
                ->latest()
                ->first();
            if (!$last) return true;
            return Carbon::parse($last->created_at)->diffInDays(now()) >= 30;
        }

        if ($rule === '1x_per_lokasi_tanggal') {
            $lokasi = $meta['lokasi'] ?? null;
            $tanggal = $meta['tanggal'] ?? null;
            if (!$lokasi || !$tanggal) return false;

            $exists = PointTransaction::where('user_id', $userId)
                ->where('related_type', 'partisipasi')
                ->where('jenis_aksi', 'baru')
                ->where('status', 'valid')
                ->whereDate('created_at', $tanggal)
                ->whereHas('user.partisipasi', function ($q) use ($lokasi, $tanggal) {
                    $q->where('lokasi_observasi', $lokasi)
                      ->whereDate('tanggal_observasi', $tanggal);
                })
                ->exists();
            return !$exists;
        }

        return true;
    }

    /**
     * Update denormalisasi total_poin pada tabel users
     */
    public static function updateTotalPoin(int $userId): void
    {
        $total = PointTransaction::where('user_id', $userId)
            ->where('status', 'valid')
            ->sum('poin');

        User::where('id', $userId)->update(['total_poin' => $total]);
    }

    /**
     * Cek dan berikan lencana otomatis
     */
    public static function cekDanBerikanLencana(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) return;

        $badges = Badge::all();

        foreach ($badges as $badge) {
            // Skip jika sudah memiliki lencana ini
            if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            if (self::syaratLencanaTerpemuhi($user, $badge->kode)) {
                UserBadge::create([
                    'user_id'   => $userId,
                    'badge_id'  => $badge->id,
                    'earned_at' => now(),
                ]);

                // Notifikasi lencana baru
                UserNotification::create([
                    'user_id' => $userId,
                    'type' => 'badge',
                    'title' => 'Lencana Baru: ' . $badge->nama,
                    'message' => $badge->deskripsi,
                    'data' => ['badge_id' => $badge->id, 'badge_kode' => $badge->kode],
                ]);
            }
        }
    }

    /**
     * Evaluasi syarat perolehan lencana
     */
    protected static function syaratLencanaTerpemuhi(User $user, string $kodeLencana): bool
    {
        return match ($kodeLencana) {
            'sensor_warga_aktif' => PointTransaction::where('user_id', $user->id)
                ->where('status', 'valid')
                ->exists(),

            'penjaga_sarpras' => PointTransaction::where('user_id', $user->id)
                ->where('related_type', 'prasarana')
                ->where('jenis_aksi', 'baru')
                ->where('status', 'valid')
                ->distinct('related_id')
                ->count('related_id') >= 5,

            'pemantau_konsisten' => PointTransaction::where('user_id', $user->id)
                ->where('related_type', 'partisipasi')
                ->where('status', 'valid')
                ->selectRaw('COUNT(DISTINCT YEARWEEK(created_at)) as minggu')
                ->value('minggu') >= 4,

            'pahlawan_data_olahraga' => (
                PointTransaction::where('user_id', $user->id)->where('status', 'valid')->sum('poin') >= 500
                || (
                    PointTransaction::where('user_id', $user->id)->where('related_type', 'prasarana')->where('status', 'valid')->exists()
                    && PointTransaction::where('user_id', $user->id)->where('related_type', 'partisipasi')->where('status', 'valid')->exists()
                    && PointTransaction::where('user_id', $user->id)->where('related_type', 'club')->where('status', 'valid')->exists()
                    && PointTransaction::where('user_id', $user->id)->where('related_type', 'event')->where('status', 'valid')->exists()
                )
            ),

            default => false,
        };
    }

    /**
     * Seed data lencana default (jalankan sekali saat setup)
     */
    public static function seedBadges(): void
    {
        $badges = [
            [
                'kode' => 'sensor_warga_aktif',
                'nama' => 'Sensor Warga Aktif',
                'deskripsi' => 'Laporan pertama tervalidasi pada kategori apapun.',
                'syarat_otomatis' => ['type' => 'first_valid_report'],
            ],
            [
                'kode' => 'penjaga_sarpras',
                'nama' => 'Penjaga Sarpras',
                'deskripsi' => 'Melaporkan minimal 5 prasarana unik tervalidasi.',
                'syarat_otomatis' => ['type' => 'prasarana_count', 'min' => 5],
            ],
            [
                'kode' => 'pemantau_konsisten',
                'nama' => 'Pemantau Konsisten',
                'deskripsi' => 'Melaporkan partisipasi pada minimal 4 minggu kalender yang berbeda.',
                'syarat_otomatis' => ['type' => 'partisipasi_weeks', 'min' => 4],
            ],
            [
                'kode' => 'pahlawan_data_olahraga',
                'nama' => 'Pahlawan Data Olahraga',
                'deskripsi' => 'Total poin mencapai 500 atau berkontribusi valid pada keempat kategori data.',
                'syarat_otomatis' => ['type' => 'poin_or_all_categories', 'min_poin' => 500],
            ],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['kode' => $badge['kode']], $badge);
        }
    }
}
