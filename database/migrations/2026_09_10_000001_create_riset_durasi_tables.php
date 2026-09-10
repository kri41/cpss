<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Instrumentasi pengukuran waktu untuk pembandingan alur kerja
 * (konvensional vs aplikasi) pada penelitian disertasi.
 *
 *  - durasi_entri      : lama pengisian satu formulir (mulai input → simpan)
 *  - pembuatan_laporan : catatan setiap kali laporan dihasilkan/diunduh
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('durasi_entri', function (Blueprint $table) {
            $table->id();
            $table->string('entri_type', 30);          // prasarana|events|clubs|partisipasi|kampung_olahraga
            $table->unsignedBigInteger('entri_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('mulai_input_at');
            $table->timestamp('selesai_input_at');     // = created_at record
            $table->unsignedInteger('durasi_detik');
            $table->enum('sumber', ['terukur', 'estimasi'])->default('terukur');
            $table->timestamps();

            $table->unique(['entri_type', 'entri_id']);
            $table->index(['user_id', 'selesai_input_at']);
        });

        Schema::create('pembuatan_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();       // pembuat (null = via tautan publik)
            $table->string('jenis', 40);              // prasarana|events|...|dashboard|laporan-relawan|rekap-relawan|live
            $table->foreignId('subjek_user_id')->nullable()->constrained('users')->nullOnDelete(); // relawan yang dilaporkan
            $table->string('format', 10)->default('pdf');
            $table->unsignedInteger('render_ms')->nullable();
            $table->timestamp('dibuat_at');
            $table->timestamps();

            $table->index(['subjek_user_id', 'dibuat_at']);
            $table->index(['jenis', 'dibuat_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembuatan_laporan');
        Schema::dropIfExists('durasi_entri');
    }
};
