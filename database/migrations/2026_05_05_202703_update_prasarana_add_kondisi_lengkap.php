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
            // Hapus kolom kondisi_lantai lama (enum) jika ada
            // dan ganti dengan struktur baru
            
            // Alamat lengkap
            $table->text('alamat')->nullable()->after('longitude');
            
            // Kondisi dengan rating 1-5 (ganti dari enum)
            // Hapus yang lama dulu jika ada
            if (Schema::hasColumn('prasarana', 'kondisi_lantai')) {
                $table->dropColumn('kondisi_lantai');
            }
            
            $table->tinyInteger('kondisi_lantai')->nullable()->after('alamat');
            $table->tinyInteger('kondisi_ring')->nullable()->after('kondisi_lantai');
            $table->tinyInteger('kondisi_net')->nullable()->after('kondisi_ring');
            $table->tinyInteger('kondisi_gawang')->nullable()->after('kondisi_net');
            $table->tinyInteger('kondisi_lapangan')->nullable()->after('kondisi_gawang');
            $table->tinyInteger('kondisi_ventilasi')->nullable()->after('kondisi_lapangan');
            $table->tinyInteger('kondisi_pencahayaan')->nullable()->after('kondisi_ventilasi');
            $table->tinyInteger('kondisi_kamar_mandi')->nullable()->after('kondisi_pencahayaan');
            
            // Akses tambahan
            $table->boolean('akses_parkir')->default(false)->after('kondisi_kamar_mandi');
            $table->boolean('akses_transportasi')->default(false)->after('akses_parkir');
            
            // Fasilitas
            $table->boolean('fasilitas_ruang_ganti')->default(false)->after('akses_transportasi');
            $table->boolean('fasilitas_tribun')->default(false)->after('fasilitas_ruang_ganti');
            
            // Foto tambahan
            $table->json('foto_tambahan')->nullable()->after('foto_path');
            
            // Keterangan
            $table->text('keterangan')->nullable()->after('foto_tambahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prasarana', function (Blueprint $table) {
            $table->dropColumn([
                'alamat',
                'kondisi_ring',
                'kondisi_net',
                'kondisi_gawang',
                'kondisi_lapangan',
                'kondisi_ventilasi',
                'kondisi_pencahayaan',
                'kondisi_kamar_mandi',
                'akses_parkir',
                'akses_transportasi',
                'fasilitas_ruang_ganti',
                'fasilitas_tribun',
                'foto_tambahan',
                'keterangan',
            ]);
            
            // Kembalikan kondisi_lantai ke enum
            $table->enum('kondisi_lantai', ['Baik', 'Sedang', 'Rusak Berat'])->after('longitude');
        });
    }
};
