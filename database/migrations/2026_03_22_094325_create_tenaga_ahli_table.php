<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenaga_ahli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Admin yang input
            $table->string('nama_tenaga_ahli');
            $table->enum('profesi', ['Pelatih', 'Wasit/Juri', 'Guru PJOK', 'Instruktur Senam']);
            $table->string('nomor_sertifikat')->unique()->nullable();
            $table->enum('tingkat_lisensi', ['Daerah', 'Nasional', 'Internasional', 'Belum Berlisensi']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenaga_ahli');
    }
};
