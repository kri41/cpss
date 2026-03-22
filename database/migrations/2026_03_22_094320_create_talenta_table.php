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
        Schema::create('talenta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Admin yang input
            $table->string('nama_atlet');
            $table->string('cabang_olahraga');
            $table->string('asal_sekolah_atau_klub');
            $table->string('prestasi_tertinggi')->nullable();
            $table->enum('status_pembinaan', ['Aktif PPLP', 'Mandiri', 'Lulus']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talenta');
    }
};
