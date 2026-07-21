<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Disederhanakan: "usulan perubahan" bukan lagi soal mengusulkan nilai field baru
     * (rawan salah karena pengusul belum tentu tahu data yang benar) — cukup permintaan
     * alasan, admin setuju → status_validasi kembali ke pending sehingga PEMILIK ASLI
     * (bukan pengusul) yang mendapat kembali akses edit langsung lewat aturan lama.
     */
    public function up(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn('perubahan');
        });
    }

    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->json('perubahan')->nullable();
        });
    }
};
