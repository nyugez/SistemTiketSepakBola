<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun Admin
        User::firstOrCreate(
            ['email' => 'admin@tiket.com'],
            [
                'name'     => 'Admin Sistem',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Buat akun User biasa untuk testing
        User::firstOrCreate(
            ['email' => 'user@tiket.com'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        // Jalankan seeder lainnya
        $this->call([
            StadiumSeeder::class,
            FootballMatchSeeder::class,
        ]);
    }
}
