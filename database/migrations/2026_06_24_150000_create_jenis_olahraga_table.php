<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_olahraga', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('kategori')->nullable(); // Permainan, Atletik, Beladiri, dll
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Seed data awal
        $olahraga = [
            ['nama' => 'Sepak Bola', 'kategori' => 'Permainan'],
            ['nama' => 'Futsal', 'kategori' => 'Permainan'],
            ['nama' => 'Bola Basket', 'kategori' => 'Permainan'],
            ['nama' => 'Bola Voli', 'kategori' => 'Permainan'],
            ['nama' => 'Badminton', 'kategori' => 'Permainan'],
            ['nama' => 'Tenis Meja', 'kategori' => 'Permainan'],
            ['nama' => 'Tenis Lapangan', 'kategori' => 'Permainan'],
            ['nama' => 'Renang', 'kategori' => 'Atletik'],
            ['nama' => 'Atletik/Lari', 'kategori' => 'Atletik'],
            ['nama' => 'Senam', 'kategori' => 'Atletik'],
            ['nama' => 'Pencak Silat', 'kategori' => 'Beladiri'],
            ['nama' => 'Taekwondo', 'kategori' => 'Beladiri'],
            ['nama' => 'Karate', 'kategori' => 'Beladiri'],
            ['nama' => 'Panahan', 'kategori' => 'Keterampilan'],
            ['nama' => 'Sepeda', 'kategori' => 'Atletik'],
            ['nama' => 'Catur', 'kategori' => 'Otak'],
            ['nama' => 'Lainnya', 'kategori' => null],
        ];

        foreach ($olahraga as $o) {
            \Illuminate\Support\Facades\DB::table('jenis_olahraga')->insert(array_merge($o, [
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_olahraga');
    }
};
