<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin already exists
        if (!User::where('email', 'admin@kasir.test')->exists()) {
            User::create([
                'name' => 'Admin Kasir',
                'email' => 'admin@kasir.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
