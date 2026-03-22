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
        Schema::create('prasarana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Siapa yang lapor
            $table->string('nama_fasilitas');
            $table->string('kategori_olahraga'); // Sepakbola, Voli, dll
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('kondisi_lantai', ['Baik', 'Sedang', 'Rusak Berat']);
            $table->boolean('akses_disabilitas')->default(false);
            $table->string('foto_path')->nullable(); // URL foto bukti
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prasarana');
    }
};
