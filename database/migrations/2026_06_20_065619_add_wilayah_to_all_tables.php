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
        Schema::table('users', function (Blueprint $table) {
            $table->string('desa')->nullable()->after('role');
            $table->string('kecamatan')->nullable()->after('desa');
            $table->string('kabupaten')->nullable()->after('kecamatan');
        });

        Schema::table('prasarana', function (Blueprint $table) {
            $table->string('desa')->nullable()->after('alamat');
            $table->string('kecamatan')->nullable()->after('desa');
            $table->string('kabupaten')->nullable()->after('kecamatan');
        });

        Schema::table('partisipasi', function (Blueprint $table) {
            $table->string('desa')->nullable()->after('lokasi_observasi');
            $table->string('kecamatan')->nullable()->after('desa');
            $table->string('kabupaten')->nullable()->after('kecamatan');
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->string('desa')->nullable()->after('alamat');
            $table->string('kecamatan')->nullable()->after('desa');
            $table->string('kabupaten')->nullable()->after('kecamatan');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('desa')->nullable()->after('deskripsi_kegiatan');
            $table->string('kecamatan')->nullable()->after('desa');
            $table->string('kabupaten')->nullable()->after('kecamatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['users', 'prasarana', 'partisipasi', 'clubs', 'events'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->dropColumn(['desa', 'kecamatan', 'kabupaten']);
            });
        }
    }
};
