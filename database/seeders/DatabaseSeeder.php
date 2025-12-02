<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\BarangSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Default pegawai (admin kasir) untuk login awal
        User::updateOrCreate(
            ['email' => 'admin@kasir.local'],
            [
                'name' => 'Admin Kasir',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'pegawai@kasir.local'],
            [
                'name' => 'Pegawai Kasir',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Tidak ada seed kategori karena kategori disimpan di kolom tabel barang

        // (Opsional) BarangSeeder dinonaktifkan sementara karena struktur barang terkini berbeda
        // Aktifkan kembali setelah menyesuaikan seeder dengan skema terbaru
        // $this->call([
        //     BarangSeeder::class,
        // ]);
    }
}
