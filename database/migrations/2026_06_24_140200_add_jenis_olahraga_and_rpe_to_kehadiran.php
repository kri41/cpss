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
        Schema::table('kehadiran', function (Blueprint $table) {
            if (!Schema::hasColumn('kehadiran', 'jenis_olahraga')) {
                $table->string('jenis_olahraga', 100)->nullable()->after('nama_peserta');
            }
            if (!Schema::hasColumn('kehadiran', 'rpe')) {
                $table->tinyInteger('rpe')->nullable()->after('jenis_olahraga');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kehadiran', function (Blueprint $table) {
            if (Schema::hasColumn('kehadiran', 'jenis_olahraga')) {
                $table->dropColumn('jenis_olahraga');
            }
            if (Schema::hasColumn('kehadiran', 'rpe')) {
                $table->dropColumn('rpe');
            }
        });
    }
};
