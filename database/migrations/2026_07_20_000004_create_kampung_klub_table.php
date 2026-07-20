<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kampung_klub', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kampung_olahraga_id')->constrained('kampung_olahraga')->cascadeOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['kampung_olahraga_id', 'club_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kampung_klub');
    }
};
