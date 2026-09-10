<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\DurasiEntri;
use App\Models\Event;
use App\Models\KampungOlahraga;
use App\Models\Partisipasi;
use App\Models\Prasarana;
use Illuminate\Console\Command;

/**
 * Mengisi tabel durasi_entri untuk data lama (dibuat sebelum instrumentasi
 * pengukuran waktu). Nilai bersifat ESTIMASI (sumber='estimasi').
 *
 * Setiap entri diberi durasi = median modul ± simpangan acak (deterministik
 * per-record, seed = id) sehingga tidak ada dua entri yang sama persis —
 * umumnya ± 1 menit dari median. mulai_input_at = created_at − durasi.
 *
 * Contoh:
 *   php artisan cpss:backfill-durasi-entri --dry-run
 *   php artisan cpss:backfill-durasi-entri --jitter=90
 *   php artisan cpss:backfill-durasi-entri --prasarana=360 --events=180
 */
class BackfillDurasiEntri extends Command
{
    protected $signature = 'cpss:backfill-durasi-entri
        {--dry-run : Tampilkan perkiraan tanpa menyimpan}
        {--jitter=75 : Simpangan maksimum ± detik dari median (75 ≈ 1,25 menit)}
        {--prasarana= : Override median detik untuk prasarana}
        {--clubs= : Override median detik untuk clubs}
        {--events= : Override median detik untuk events}
        {--partisipasi= : Override median detik untuk partisipasi}
        {--kampung_olahraga= : Override median detik untuk kampung_olahraga}';

    protected $description = 'Isi durasi_entri (estimasi) untuk data pelaporan lama';

    private const MODEL = [
        'prasarana' => Prasarana::class,
        'clubs' => Club::class,
        'events' => Event::class,
        'partisipasi' => Partisipasi::class,
        'kampung_olahraga' => KampungOlahraga::class,
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $jitter = max(0, (int) $this->option('jitter'));

        $sudahAda = DurasiEntri::query()
            ->get(['entri_type', 'entri_id'])
            ->groupBy('entri_type')
            ->map(fn ($g) => $g->pluck('entri_id')->flip());

        $ringkas = [];
        $totalBaru = 0;
        $totalDetik = 0;
        $contoh = [];

        foreach (self::MODEL as $type => $modelClass) {
            $median = (int) ($this->option($type) ?: DurasiEntri::ESTIMASI_MEDIAN[$type] ?? 120);
            $floor = max(20, (int) round($median * 0.40));
            $existing = $sudahAda[$type] ?? collect();

            $baris = [];
            $count = 0;
            $sumDetik = 0;

            $modelClass::query()
                ->select(['id', 'user_id', 'created_at'])
                ->orderBy('id')
                ->get()
                ->each(function ($r) use ($type, $median, $jitter, $floor, $existing, &$baris, &$count, &$sumDetik, &$contoh) {
                    if ($existing->has($r->id)) {
                        return;
                    }

                    // simpangan acak deterministik per-record (triangular, puncak di 0)
                    mt_srand((int) ($r->id * 2654435761 + crc32($type)) & 0x7FFFFFFF);
                    $u = (mt_rand(0, 2000) + mt_rand(0, 2000)) / 2000 - 1; // [-1, 1], cenderung ke 0
                    $offset = (int) round($u * $jitter);
                    $durasi = max($floor, $median + $offset);

                    $selesai = $r->created_at->copy();
                    $mulai = $selesai->copy()->subSeconds($durasi);

                    $baris[] = [
                        'entri_type' => $type,
                        'entri_id' => $r->id,
                        'user_id' => $r->user_id,
                        'mulai_input_at' => $mulai,
                        'selesai_input_at' => $selesai,
                        'durasi_detik' => $durasi,
                        'sumber' => 'estimasi',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $count++;
                    $sumDetik += $durasi;

                    if (count($contoh) < 8) {
                        $contoh[] = [DurasiEntri::LABEL[$type] ?? $type, $durasi.' dtk', round($durasi / 60, 1).' mnt'];
                    }
                });

            if (! $dry && $baris) {
                foreach (array_chunk($baris, 500) as $chunk) {
                    DurasiEntri::insert($chunk);
                }
            }

            $ringkas[] = [
                DurasiEntri::LABEL[$type] ?? $type,
                $median.' dtk (±'.$jitter.')',
                $count,
                $count ? round($sumDetik / $count).' dtk' : '-',
                $count ? round($sumDetik / 60).' mnt' : '-',
            ];
            $totalBaru += $count;
            $totalDetik += $sumDetik;
        }

        $this->newLine();
        $this->table(['Modul', 'Median', 'Baris baru', 'Rata-rata', 'Total'], $ringkas);

        if ($contoh) {
            $this->newLine();
            $this->line('<comment>Contoh nilai (bervariasi tiap entri):</comment>');
            $this->table(['Modul', 'Durasi', ''], $contoh);
        }

        $this->newLine();
        if ($dry) {
            $this->warn("DRY-RUN: {$totalBaru} baris akan dibuat (≈ ".round($totalDetik / 60).' menit total). Jalankan tanpa --dry-run untuk menyimpan.');
        } else {
            $this->info("Selesai: {$totalBaru} baris estimasi dibuat (≈ ".round($totalDetik / 60).' menit total).');
            $this->line('Data terukur (sumber=terukur) tidak disentuh.');
        }

        return self::SUCCESS;
    }
}
