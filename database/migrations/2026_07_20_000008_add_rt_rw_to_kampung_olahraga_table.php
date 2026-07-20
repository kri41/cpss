<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RT/RW memungkinkan Kampung Olahraga didaftarkan pada skala kecil (lingkungan),
     * bukan hanya satu per desa — sehingga beberapa relawan di desa yang sama tetap
     * bisa mendaftarkan kampungnya masing-masing dan mendapatkan penghargaan/poin.
     */
    public function up(): void
    {
        Schema::table('kampung_olahraga', function (Blueprint $table) {
            $table->string('rt', 5)->nullable()->after('desa');
            $table->string('rw', 5)->nullable()->after('rt');
        });
    }

    public function down(): void
    {
        Schema::table('kampung_olahraga', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw']);
        });
    }
};
