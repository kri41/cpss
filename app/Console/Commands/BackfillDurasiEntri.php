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
 * pengukuran waktu). Nilai bersifat ESTIMASI (sumber='estimasi') berdasarkan
 * median waktu pengisian per modul + variasi acak deterministik per-record
 * (mt_srand = id) agar tidak seragam dan tetap reproducible.
 *
 * Contoh:
 *   php artisan cpss:backfill-durasi-entri --dry-run
 *   php artisan cpss:backfill-durasi-entri --prasarana=360 --events=180
 */
class BackfillDurasiEntri extends Command
{
    protected $signature = 'cpss:backfill-durasi-entri
        {--dry-run : Tampilkan perkiraan tanpa menyimpan}
        {--spread=0.30 : Variasi acak ± (0.30 = ±30%)}
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
        $spread = max(0.0, min(0.9, (float) $this->option('spread')));

        $sudahAda = DurasiEntri::query()
            ->get(['entri_type', 'entri_id'])
            ->groupBy('entri_type')
            ->map(fn ($g) => $g->pluck('entri_id')->flip());

        $ringkas = [];
        $totalBaru = 0;
        $totalDetik = 0;

        foreach (self::MODEL as $type => $modelClass) {
            $median = (int) ($this->option($type) ?: DurasiEntri::ESTIMASI_MEDIAN[$type] ?? 120);
            $existing = $sudahAda[$type] ?? collect();

            $count = 0;
            $sumDetik = 0;

            $modelClass::query()
                ->select(['id', 'user_id', 'created_at'])
                ->orderBy('user_id')->orderBy('created_at')
                ->chunkById(500, function ($rows) use ($type, $median, $spread, $existing, $dry, &$count, &$sumDetik) {
                    $baris = [];
                    foreach ($rows as $r) {
                        if ($existing->has($r->id)) {
                            continue;
                        }

                        mt_srand($r->id + crc32($type));
                        $faktor = 1 + (mt_rand(-1000, 1000) / 1000) * $spread;
                        $durasi = max(15, (int) round($median * $faktor));

                        $selesai = $r->created_at;
                        $mulai = $selesai->copy()->subSeconds($durasi);

                        $count++;
                        $sumDetik += $durasi;

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
                    }

                    if (! $dry && $baris) {
                        DurasiEntri::insert($baris);
                    }
                });

            $ringkas[] = [DurasiEntri::LABEL[$type] ?? $type, $median.' dtk', $count, $sumDetik ? round($sumDetik / 60) : 0];
            $totalBaru += $count;
            $totalDetik += $sumDetik;
        }

        $this->table(['Modul', 'Median', 'Baris baru', 'Total menit'], $ringkas);
        $this->newLine();

        if ($dry) {
            $this->warn("DRY-RUN: {$totalBaru} baris akan dibuat (".round($totalDetik / 60).' menit total estimasi). Jalankan tanpa --dry-run untuk menyimpan.');
        } else {
            $this->info("Selesai: {$totalBaru} baris estimasi dibuat (".round($totalDetik / 60).' menit total).');
            $this->line('Data terukur (sumber=terukur) tidak disentuh.');
        }

        return self::SUCCESS;
    }
}
