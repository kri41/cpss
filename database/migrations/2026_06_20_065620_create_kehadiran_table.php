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
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partisipasi_id')->constrained('partisipasi')->onDelete('cascade');
            $table->string('nama_peserta');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->integer('usia')->nullable();
            $table->enum('kelompok_usia', ['Anak', 'Remaja', 'Dewasa', 'Lansia'])->nullable();
            $table->string('kategori_khusus')->nullable(); // disabilitas, ibu hamil, dll
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alfa'])->default('Hadir');
            $table->string('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};
