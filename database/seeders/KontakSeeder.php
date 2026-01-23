<?php

namespace Database\Seeders;

use App\Models\Kontak;
use Illuminate\Database\Seeder;

class KontakSeeder extends Seeder
{
    public function run(): void
    {
        Kontak::create([
            'name' => 'PT Sumber Makmur',
            'tipe' => 'VENDOR',
            'hp' => '081234567890',
            'alamat' => 'Jl. Industri No. 1, Jakarta',
        ]);

        Kontak::create([
            'name' => 'Toko Kelontong Berkah',
            'tipe' => 'VENDOR',
            'hp' => '089876543210',
            'alamat' => 'Jl. Pasar Baru No. 5, Bandung',
        ]);

        Kontak::create([
            'name' => 'Budi Santoso',
            'tipe' => 'CUSTOMER',
            'hp' => '085678901234',
            'alamat' => 'Perumahan Griya Indah, Blok A1',
        ]);

        Kontak::create([
            'name' => 'Siti Aminah',
            'tipe' => 'CUSTOMER',
            'hp' => '081122334455',
            'alamat' => 'Jl. Merdeka No. 45',
        ]);
    }
}
