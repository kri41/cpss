<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreignId('jenis_olahraga_id')->nullable()->after('nama_club')
                ->constrained('jenis_olahraga')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jenis_olahraga_id');
        });
    }
};
