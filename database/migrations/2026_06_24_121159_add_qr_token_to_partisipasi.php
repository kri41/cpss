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
        Schema::table('partisipasi', function (Blueprint $table) {
            if (!Schema::hasColumn('partisipasi', 'qr_token')) {
                $table->string('qr_token', 64)->nullable()->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('partisipasi', function (Blueprint $table) {
            if (Schema::hasColumn('partisipasi', 'qr_token')) {
                $table->dropColumn('qr_token');
            }
        });
    }
};
