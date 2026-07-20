<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen_syarat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('target_checkin')->default(10);
            $table->integer('poin')->default(5);
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        $komponens = [
            ['nama' => 'Partisipasi Warga Awal',       'deskripsi' => 'Minimal 10 warga aktif berolahraga',   'target_checkin' => 10,  'poin' => 5,  'urutan' => 1],
            ['nama' => 'Partisipasi Warga Berkembang',  'deskripsi' => 'Minimal 50 warga aktif berolahraga',   'target_checkin' => 50,  'poin' => 15, 'urutan' => 2],
            ['nama' => 'Partisipasi Warga Aktif',       'deskripsi' => 'Minimal 100 warga aktif berolahraga',  'target_checkin' => 100, 'poin' => 30, 'urutan' => 3],
            ['nama' => 'Kampung Olahraga Produktif',    'deskripsi' => 'Minimal 250 warga aktif berolahraga',  'target_checkin' => 250, 'poin' => 50, 'urutan' => 4],
        ];

        foreach ($komponens as $k) {
            \Illuminate\Support\Facades\DB::table('komponen_syarat')->insert(array_merge($k, [
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('komponen_syarat');
    }
};
