<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * status_validasi di prasarana/clubs/events/partisipasi awalnya cuma
     * ENUM('pending','validated') — fitur "Butuh Perbaikan" butuh nilai
     * 'rejected' juga (pola yang sudah dipakai kampung_olahraga sejak awal).
     * Doctrine/DBAL tidak dipakai di proyek ini, jadi ALTER enum lewat
     * raw SQL, bukan Schema::table()->change().
     */
    public function up(): void
    {
        $tables = ['prasarana', 'clubs', 'events', 'partisipasi'];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} MODIFY status_validasi ENUM('pending', 'validated', 'rejected') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        $tables = ['prasarana', 'clubs', 'events', 'partisipasi'];

        foreach ($tables as $table) {
            DB::statement("UPDATE {$table} SET status_validasi = 'pending' WHERE status_validasi = 'rejected'");
            DB::statement("ALTER TABLE {$table} MODIFY status_validasi ENUM('pending', 'validated') NOT NULL DEFAULT 'pending'");
        }
    }
};
