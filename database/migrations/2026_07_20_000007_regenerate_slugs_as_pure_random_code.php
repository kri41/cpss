<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Ganti slug lama (nama-slug + suffix acak) menjadi kode unik acak murni
     * 10 karakter, konsisten di semua tabel — permintaan revisi: URL cukup kode
     * unik saja tanpa embed nama.
     */
    private array $tables = ['prasarana', 'clubs', 'events', 'kampung_olahraga', 'users'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            $ids = DB::table($table)->pluck('id');

            foreach ($ids as $id) {
                do {
                    $slug = Str::lower(Str::random(10));
                } while (DB::table($table)->where('slug', $slug)->exists());

                DB::table($table)->where('id', $id)->update(['slug' => $slug]);
            }
        }
    }

    public function down(): void
    {
        // Tidak ada rollback bermakna — slug lama (nama-slug) tidak disimpan.
    }
};
