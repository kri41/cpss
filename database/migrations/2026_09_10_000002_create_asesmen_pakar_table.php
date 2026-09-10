<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hasil transkripsi lembar audit ASESOR PAKAR (kerja di kertas, blind).
 * Dibandingkan dengan data prasarana versi relawan untuk menghitung
 * kesepakatan antar-penilai (Cohen's Kappa, RM3) & efisiensi waktu (RM4).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asesmen_pakar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prasarana_id')->constrained('prasarana')->cascadeOnDelete();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();

            // Identitas asesor & waktu kerja (dari lembar kertas)
            $table->string('nama_asesor');
            $table->date('tanggal_asesmen')->nullable();
            $table->timestamp('waktu_mulai')->nullable();          // T1 — mulai menilai lapangan
            $table->timestamp('waktu_selesai')->nullable();        // T1 — selesai menilai lapangan
            $table->unsignedInteger('durasi_laporan_detik')->nullable(); // T2 — lama menyusun laporan/lembar

            // Penilaian kondisi (1-5) — cermin kolom prasarana
            $table->unsignedTinyInteger('kondisi_lantai')->nullable();
            $table->unsignedTinyInteger('kondisi_ring')->nullable();
            $table->unsignedTinyInteger('kondisi_net')->nullable();
            $table->unsignedTinyInteger('kondisi_gawang')->nullable();
            $table->unsignedTinyInteger('kondisi_lapangan')->nullable();
            $table->unsignedTinyInteger('kondisi_ventilasi')->nullable();
            $table->unsignedTinyInteger('kondisi_pencahayaan')->nullable();
            $table->unsignedTinyInteger('kondisi_kamar_mandi')->nullable();

            // Aksesibilitas & kelengkapan (null = tidak dinilai, 0 = tidak, 1 = ya)
            $table->boolean('akses_disabilitas')->nullable();
            $table->boolean('akses_parkir')->nullable();
            $table->boolean('akses_transportasi')->nullable();
            $table->boolean('fasilitas_ruang_ganti')->nullable();
            $table->boolean('fasilitas_tribun')->nullable();

            // Kategori olahraga versi asesor (daftar id jenis_olahraga)
            $table->json('kategori_olahraga_ids')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('prasarana_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asesmen_pakar');
    }
};
