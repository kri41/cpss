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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('prasarana_id')->nullable()->constrained('prasarana')->onDelete('set null');
            $table->string('nama_club');
            $table->text('deskripsi')->nullable();
            $table->string('ketua_club');
            $table->string('narahubung');
            $table->string('no_telepon');
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('aktif')->default(true);
            $table->date('tanggal_berdiri')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
