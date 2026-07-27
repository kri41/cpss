<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diskusi_posts', function (Blueprint $table) {
            $table->id();
            $table->string('provinsi'); // kode BPS provinsi (2 digit), scope papan diskusi
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('pesan');
            $table->timestamps();

            $table->index('provinsi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskusi_posts');
    }
};
