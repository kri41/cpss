<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Grandfather: semua user yang sudah ada dianggap terverifikasi
        //    supaya pengaktifan email verification tidak mengunci akun lama.
        DB::table('users')->whereNull('email_verified_at')->update([
            'email_verified_at' => now(),
        ]);

        // 2) Token unik untuk link Live Report publik per relawan
        if (! Schema::hasColumn('users', 'public_report_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('public_report_token', 64)->nullable()->unique()->after('slug');
            });
        }

        foreach (DB::table('users')->where('role', 'relawan')->whereNull('public_report_token')->pluck('id') as $id) {
            do {
                $token = Str::random(48);
            } while (DB::table('users')->where('public_report_token', $token)->exists());

            DB::table('users')->where('id', $id)->update(['public_report_token' => $token]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'public_report_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('public_report_token');
            });
        }
    }
};
