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
        Schema::table('prasarana', function (Blueprint $table) {
            $table->string('club_komunitas')->nullable()->after('nama_fasilitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prasarana', function (Blueprint $table) {
            $table->dropColumn('club_komunitas');
        });
    }
};
