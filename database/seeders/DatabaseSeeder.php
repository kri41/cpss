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
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@cpss.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin Dispora',
            'email' => 'admin@cpss.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Relawan
        User::create([
            'name' => 'Relawan 1',
            'email' => 'relawan@cpss.test',
            'password' => Hash::make('password'),
            'role' => 'relawan',
        ]);
    }
}
