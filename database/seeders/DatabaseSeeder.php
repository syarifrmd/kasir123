<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        User::updateOrCreate(
            ['email' => 'admin@kasir.local'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Kasir
        User::updateOrCreate(
            ['email' => 'kasir@kasir.local'],
            [
                'name' => 'Kasir Utama',
                'password' => bcrypt('password'),
                'role' => 'kasir',
                'email_verified_at' => now(),
            ]
        );

        // Call other seeders
        $this->call([
            KontakSeeder::class,
            BarangSeeder::class,
            TransaksiSeeder::class,
        ]);
    }
}
