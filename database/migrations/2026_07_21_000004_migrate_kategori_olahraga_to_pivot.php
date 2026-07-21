<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prasarana dulu cuma bisa punya satu "kategori_olahraga" (teks bebas) dan
     * satu "club_komunitas" (teks bebas, arah link yang salah — seharusnya
     * Club yang memilih Prasarana, bukan sebaliknya, dan itu sudah ada lewat
     * Club::prasarana_id). Migrasi ini memindahkan nilai kategori_olahraga
     * yang sudah ada ke relasi many-to-many jenis_olahraga (mendukung lebih
     * dari satu cabang olahraga per fasilitas), lalu membuang kedua kolom lama.
     */
    public function up(): void
    {
        DB::table('prasarana')->orderBy('id')->get(['id', 'kategori_olahraga'])->each(function ($row) {
            $nama = trim((string) $row->kategori_olahraga);
            if ($nama === '') {
                return;
            }

            $jenis = DB::table('jenis_olahraga')->whereRaw('LOWER(nama) = ?', [strtolower($nama)])->first();
            $jenisId = $jenis->id ?? DB::table('jenis_olahraga')->insertGetId([
                'nama' => $nama,
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('prasarana_jenis_olahraga')->insertOrIgnore([
                'prasarana_id' => $row->id,
                'jenis_olahraga_id' => $jenisId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Schema::table('prasarana', function (Blueprint $table) {
            $table->dropColumn(['kategori_olahraga', 'club_komunitas']);
        });
    }

    public function down(): void
    {
        Schema::table('prasarana', function (Blueprint $table) {
            $table->string('kategori_olahraga')->nullable()->after('nama_fasilitas');
            $table->string('club_komunitas')->nullable()->after('nama_fasilitas');
        });
    }
};
