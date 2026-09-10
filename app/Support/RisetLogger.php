<?php

namespace App\Support;

use App\Models\DurasiEntri;
use App\Models\PembuatanLaporan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

/**
 * Pencatat data pengukuran waktu untuk penelitian disertasi
 * (pembandingan alur kerja konvensional vs aplikasi).
 *
 * Semua metode dibungkus try/catch: kegagalan pencatatan riset tidak boleh
 * mengganggu alur utama pengguna.
 */
class RisetLogger
{
    /** Batas wajar durasi "terukur": < 5 detik atau > 2 jam dianggap tidak valid. */
    private const MIN_WAJAR = 5;

    private const MAKS_WAJAR = 7200;

    /**
     * Catat lama pengisian satu formulir pelaporan.
     *
     * @param  string  $type  prasarana|events|clubs|partisipasi|kampung_olahraga
     * @param  string|null  $mulaiInputIso  nilai field tersembunyi "_mulai_input" (ISO-8601)
     */
    public static function catatEntri(string $type, int $id, int $userId, ?string $mulaiInputIso): void
    {
        try {
            $selesai = CarbonImmutable::now();
            $mulai = null;
            $sumber = 'terukur';

            if ($mulaiInputIso) {
                try {
                    $mulai = CarbonImmutable::parse($mulaiInputIso);
                } catch (\Throwable) {
                    $mulai = null;
                }
            }

            $durasi = $mulai ? $mulai->diffInSeconds($selesai) : null;

            // Tidak ada nilai / di luar batas wajar → pakai estimasi median modul
            if ($durasi === null || $durasi < self::MIN_WAJAR || $durasi > self::MAKS_WAJAR) {
                $durasi = DurasiEntri::ESTIMASI_MEDIAN[$type] ?? 120;
                $mulai = $selesai->subSeconds($durasi);
                $sumber = 'estimasi';
            }

            DurasiEntri::updateOrCreate(
                ['entri_type' => $type, 'entri_id' => $id],
                [
                    'user_id' => $userId,
                    'mulai_input_at' => $mulai,
                    'selesai_input_at' => $selesai,
                    'durasi_detik' => (int) round($durasi),
                    'sumber' => $sumber,
                ]
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Catat sebuah laporan dihasilkan / diunduh.
     */
    public static function catatLaporan(string $jenis, string $format = 'pdf', ?int $subjekUserId = null, ?float $renderMs = null): void
    {
        try {
            PembuatanLaporan::create([
                'user_id' => Auth::id(),
                'jenis' => $jenis,
                'subjek_user_id' => $subjekUserId,
                'format' => $format,
                'render_ms' => $renderMs !== null ? (int) round($renderMs) : null,
                'dibuat_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
