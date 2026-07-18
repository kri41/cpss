<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@cpss.test'],
            [
                'name'       => 'Super Admin Dataraga',
                'password'   => Hash::make('password'),
                'role'       => 'super_admin',
                'desa'       => 'Desa Sumber Sehat',
                'kecamatan'  => 'Kec. Tegalsari',
                'kabupaten'  => 'Kab. Banyuwangi',
            ]
        );

        // Badges diperlukan oleh sistem gamifikasi
        $this->call(BadgeSeeder::class);
    }
}
