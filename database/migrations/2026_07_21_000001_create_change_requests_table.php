<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Ajukan Perubahan" — non-pemilik/non-admin tidak bisa edit data yang sudah
     * divalidasi secara langsung, jadi perubahan yang mereka usulkan disimpan di
     * sini dulu untuk ditinjau admin (diterima → langsung update data asli,
     * ditolak → cukup dicatat alasannya).
     */
    public function up(): void
    {
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('changeable_type');
            $table->unsignedBigInteger('changeable_id');
            $table->json('perubahan');
            $table->text('alasan');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['changeable_type', 'changeable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
