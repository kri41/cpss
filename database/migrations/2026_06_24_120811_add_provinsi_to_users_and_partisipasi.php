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
        $tables = ['users', 'prasarana', 'clubs', 'events', 'partisipasi'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'provinsi')) {
                    $table->string('provinsi', 255)->nullable()->after('kabupaten');
                }
            });
        }
    }

    public function down(): void
    {
        $tables = ['users', 'prasarana', 'clubs', 'events', 'partisipasi'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'provinsi')) {
                    $table->dropColumn('provinsi');
                }
            });
        }
    }
};
