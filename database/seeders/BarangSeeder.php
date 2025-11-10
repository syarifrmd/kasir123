<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangData = [
            [
                'kategori' => 'RK',
                'nama_barang' => 'Sampoerna Mild',
                'deskripsi' => 'Rokok kretek mild',
                'ukuran_kemasan' => '1 Bungkus',
                'harga_barang' => 25000,
                'stok_barang' => 100,
            ],
            [
                'kategori' => 'MN',
                'nama_barang' => 'Aqua',
                'deskripsi' => 'Air mineral dalam kemasan',
                'ukuran_kemasan' => '600ml Botol',
                'harga_barang' => 4000,
                'stok_barang' => 150,
            ],
            [
                'kategori' => 'MN',
                'nama_barang' => 'Teh Botol Sosro',
                'deskripsi' => 'Minuman teh dalam botol',
                'ukuran_kemasan' => '350ml Botol',
                'harga_barang' => 5000,
                'stok_barang' => 120,
            ],
            [
                'kategori' => 'SB',
                'nama_barang' => 'Beras Premium',
                'deskripsi' => 'Beras putih kualitas premium',
                'ukuran_kemasan' => '5 Kg',
                'harga_barang' => 65000,
                'stok_barang' => 30,
            ],
            [
                'kategori' => 'SB',
                'nama_barang' => 'Gula Pasir',
                'deskripsi' => 'Gula pasir putih',
                'ukuran_kemasan' => '1 Kg',
                'harga_barang' => 15000,
                'stok_barang' => 50,
            ],
            [
                'kategori' => 'SK',
                'nama_barang' => 'Chitato',
                'deskripsi' => 'Keripik kentang',
                'ukuran_kemasan' => '68g',
                'harga_barang' => 10000,
                'stok_barang' => 60,
            ],
            [
                'kategori' => 'SK',
                'nama_barang' => 'Indomie Goreng',
                'deskripsi' => 'Mie instan rasa goreng',
                'ukuran_kemasan' => '1 Bungkus',
                'harga_barang' => 3500,
                'stok_barang' => 200,
            ],
            [
                'kategori' => 'OB',
                'nama_barang' => 'Paracetamol',
                'deskripsi' => 'Obat pereda nyeri dan demam',
                'ukuran_kemasan' => 'Strip 10 Tablet',
                'harga_barang' => 8000,
                'stok_barang' => 40,
            ],
            [
                'kategori' => 'EK',
                'nama_barang' => 'Baterai ABC',
                'deskripsi' => 'Baterai alkaline',
                'ukuran_kemasan' => 'AA 2pcs',
                'harga_barang' => 8000,
                'stok_barang' => 50,
            ],
        ];

        foreach ($barangData as $data) {
            Barang::create($data);
        }
    }
}
