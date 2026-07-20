<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Tabel yang mendapat kolom slug: [nama_tabel => kolom sumber nama, atau null untuk kode acak murni]
     */
    private array $tables = [
        'prasarana' => 'nama_fasilitas',
        'clubs' => 'nama_club',
        'events' => 'nama_event',
        'kampung_olahraga' => 'nama_kampung',
        'users' => null,
    ];

    public function up(): void
    {
        foreach (array_keys($this->tables) as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('slug')->nullable()->unique()->after('id');
            });
        }

        foreach ($this->tables as $table => $sourceColumn) {
            $columns = $sourceColumn ? ['id', $sourceColumn] : ['id'];
            $rows = DB::table($table)->select($columns)->get();

            foreach ($rows as $row) {
                $base = $sourceColumn ? Str::slug($row->{$sourceColumn}) : '';
                $base = $base !== '' ? $base : Str::lower(Str::random(8));

                do {
                    $slug = $base . '-' . Str::lower(Str::random(6));
                } while (DB::table($table)->where('slug', $slug)->exists());

                DB::table($table)->where('id', $row->id)->update(['slug' => $slug]);
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('slug');
            });
        }
    }
};
