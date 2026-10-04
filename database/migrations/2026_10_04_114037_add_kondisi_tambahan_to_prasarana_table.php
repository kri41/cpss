<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tidak semua prasarana punya 8 komponen baku (lantai, ring, net, dst) —
     * ada yang cuma tanah lapang. Kolom ini menampung komponen kondisi
     * tambahan yang diinput manual (nama + nilai 1-5), di luar 8 kolom baku.
     */
    public function up(): void
    {
        Schema::table('prasarana', function (Blueprint $table) {
            $table->json('kondisi_tambahan')->nullable()->after('kondisi_kamar_mandi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prasarana', function (Blueprint $table) {
            $table->dropColumn('kondisi_tambahan');
        });
    }
};
