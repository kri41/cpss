<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkin_kampung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kampung_olahraga_id')->constrained('kampung_olahraga')->cascadeOnDelete();
            $table->string('nama_peserta');
            $table->unsignedTinyInteger('umur');
            $table->foreignId('jenis_olahraga_id')->nullable()->constrained('jenis_olahraga')->nullOnDelete();
            $table->string('jenis_olahraga_nama')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkin_kampung');
    }
};
