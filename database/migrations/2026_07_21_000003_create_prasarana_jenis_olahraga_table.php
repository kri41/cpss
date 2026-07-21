<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prasarana_jenis_olahraga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prasarana_id')->constrained('prasarana')->cascadeOnDelete();
            $table->foreignId('jenis_olahraga_id')->constrained('jenis_olahraga')->cascadeOnDelete();
            $table->unique(['prasarana_id', 'jenis_olahraga_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prasarana_jenis_olahraga');
    }
};
