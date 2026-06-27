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
            if (!Schema::hasColumn('kehadiran', 'sumber')) {
                $table->string('sumber', 20)->nullable()->after('catatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kehadiran', function (Blueprint $table) {
            if (Schema::hasColumn('kehadiran', 'sumber')) {
                $table->dropColumn('sumber');
            }
        });
    }
};
