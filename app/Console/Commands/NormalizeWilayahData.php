<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NormalizeWilayahData extends Command
{
    protected $signature = 'wilayah:normalize {--dry-run : Tampilkan perubahan tanpa menyimpan}';

    protected $description = 'Normalisasi kolom provinsi/kabupaten/kecamatan/desa agar cocok dengan nama baku di tabel wilayah, dan backfill provinsi yang kosong';

    protected function normalize(string $s): string
    {
        $s = strtolower(trim($s));
        $s = preg_replace('/^(kab\.?|kabupaten|kota|kec\.?|kecamatan|desa|kel\.?|kelurahan)\s+/', '', $s);
        return trim($s);
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $kabupaten = DB::table('wilayah')->whereRaw('LENGTH(kode) = 5')->get();
        $kecamatan = DB::table('wilayah')->whereRaw('LENGTH(kode) = 8')->get();
        $desa = DB::table('wilayah')->whereRaw('LENGTH(kode) = 13')->get();

        $findKabupaten = function (string $nama) use ($kabupaten) {
            $norm = $this->normalize($nama);
            return $kabupaten->first(fn ($k) => $this->normalize($k->nama) === $norm);
        };

        // Kecamatan/desa harus dicocokkan HANYA dalam lingkup induknya (parentKode) —
        // banyak nama kecamatan/desa yang sama persis muncul di banyak wilayah berbeda
        // di Indonesia, jadi tanpa parentKode kita tidak mencocokkan sama sekali
        // (lebih baik biarkan data lama daripada salah tautkan ke wilayah lain).
        $findScoped = function (string $nama, ?string $parentKode, Collection $pool) {
            if (!$parentKode) {
                return null;
            }
            $norm = $this->normalize($nama);
            $scoped = $pool->filter(fn ($item) => str_starts_with($item->kode, $parentKode . '.'));
            return $scoped->first(fn ($item) => $this->normalize($item->nama) === $norm);
        };

        $tables = ['users', 'prasarana', 'clubs', 'events', 'partisipasi', 'kampung_olahraga'];

        foreach ($tables as $table) {
            if (!DB::getSchemaBuilder()->hasColumn($table, 'kabupaten')) {
                continue;
            }

            $rows = DB::table($table)->get();
            $updated = 0;

            foreach ($rows as $row) {
                $changes = [];
                $kabRow = null;
                $kecRow = null;

                if (!empty($row->kabupaten)) {
                    $kabRow = $findKabupaten($row->kabupaten);
                    if ($kabRow && $kabRow->nama !== $row->kabupaten) {
                        $changes['kabupaten'] = $kabRow->nama;
                    }
                }

                if ($kabRow && property_exists($row, 'provinsi') && empty($row->provinsi)) {
                    $changes['provinsi'] = explode('.', $kabRow->kode)[0];
                }

                if (!empty($row->kecamatan)) {
                    $kecRow = $findScoped($row->kecamatan, $kabRow->kode ?? null, $kecamatan);
                    if ($kecRow && $kecRow->nama !== $row->kecamatan) {
                        $changes['kecamatan'] = $kecRow->nama;
                    }
                }

                if (!empty($row->desa)) {
                    $desaRow = $findScoped($row->desa, $kecRow->kode ?? null, $desa);
                    if ($desaRow && $desaRow->nama !== $row->desa) {
                        $changes['desa'] = $desaRow->nama;
                    }
                }

                if (!empty($changes)) {
                    $this->line("[$table#{$row->id}] " . json_encode($changes));
                    if (!$dryRun) {
                        DB::table($table)->where('id', $row->id)->update($changes);
                    }
                    $updated++;
                }
            }

            $this->info("$table: $updated row(s) " . ($dryRun ? 'akan diubah' : 'diperbarui'));
        }

        return self::SUCCESS;
    }
}
