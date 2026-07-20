<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prasarana', function (Blueprint $table) {
            $table->foreignId('kampung_olahraga_id')->nullable()->after('user_id')
                ->constrained('kampung_olahraga')->nullOnDelete();
            $table->string('qr_token', 32)->nullable()->unique()->after('kampung_olahraga_id');
        });
    }

    public function down(): void
    {
        Schema::table('prasarana', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kampung_olahraga_id');
            $table->dropColumn('qr_token');
        });
    }
};
