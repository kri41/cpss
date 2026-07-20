<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkin_kampung', function (Blueprint $table) {
            $table->foreignId('prasarana_id')->nullable()->after('kampung_olahraga_id')
                ->constrained('prasarana')->nullOnDelete();
            $table->foreignId('club_id')->nullable()->after('prasarana_id')
                ->constrained('clubs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('checkin_kampung', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prasarana_id');
            $table->dropConstrainedForeignId('club_id');
        });
    }
};
