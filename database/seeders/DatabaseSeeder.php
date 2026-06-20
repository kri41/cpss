<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============================================================
        // AKUN DUMMY - FASE 1 PROTOTIPE CPSS
        // ============================================================

        // 1. Super Admin
        User::create([
            'name' => 'Super Admin CPSS',
            'email' => 'superadmin@cpss.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'desa' => 'Desa Sumber Sehat',
            'kecamatan' => 'Kec. Tegalsari',
            'kabupaten' => 'Kab. Banyuwangi',
        ]);

        // 2. Admin Daerah (2 akun)
        User::create([
            'name' => 'Admin Dispora Banyuwangi',
            'email' => 'admin@cpss.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'desa' => 'Desa Genteng Kulon',
            'kecamatan' => 'Kec. Genteng',
            'kabupaten' => 'Kab. Banyuwangi',
        ]);

        User::create([
            'name' => 'Admin Dispora Jember',
            'email' => 'admin.jember@cpss.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'desa' => 'Desa Kalisat',
            'kecamatan' => 'Kec. Kalisat',
            'kabupaten' => 'Kab. Jember',
        ]);

        // 3. Penggerak Olahraga / Relawan (5 akun)
        $relawanData = [
            [
                'name' => 'Budi Santoso',
                'email' => 'relawan@cpss.test',
                'desa' => 'Desa Sumber Agung',
                'kecamatan' => 'Kec. Glagah',
                'kabupaten' => 'Kab. Banyuwangi',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@cpss.test',
                'desa' => 'Desa Tamansari',
                'kecamatan' => 'Kec. Banyuwangi',
                'kabupaten' => 'Kab. Banyuwangi',
            ],
            [
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad.hidayat@cpss.test',
                'desa' => 'Desa Jajag',
                'kecamatan' => 'Kec. Gambiran',
                'kabupaten' => 'Kab. Banyuwangi',
            ],
            [
                'name' => 'Rina Wulandari',
                'email' => 'rina.wulandari@cpss.test',
                'desa' => 'Desa Gumukmas',
                'kecamatan' => 'Kec. Gumukmas',
                'kabupaten' => 'Kab. Jember',
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi.kurniawan@cpss.test',
                'desa' => 'Desa Tempurejo',
                'kecamatan' => 'Kec. Tempurejo',
                'kabupaten' => 'Kab. Jember',
            ],
        ];

        foreach ($relawanData as $data) {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'relawan',
                'desa' => $data['desa'],
                'kecamatan' => $data['kecamatan'],
                'kabupaten' => $data['kabupaten'],
            ]);
        }

        // Seed badges
        $this->call(BadgeSeeder::class);
    }
}
