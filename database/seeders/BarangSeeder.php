<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Gula Pasir (Kiloan, bisa diecer)
        Barang::create([
            'kode_barang' => 'SB001',
            'nama_barang' => 'Gula Pasir Putih',
            'kategori' => 'Sembako',
            'satuan' => 'kg',
            'harga_jual' => 16000,
            'stok' => 0, // Awal 0, nanti diisi via TransaksiLedger (MASUK)
        ]);

        // 2. Beras (Kiloan)
        Barang::create([
            'kode_barang' => 'SB002',
            'nama_barang' => 'Beras Rojolele',
            'kategori' => 'Sembako',
            'satuan' => 'kg',
            'harga_jual' => 14000,
            'stok' => 0,
        ]);

        // 3. Rokok (Pcs)
        Barang::create([
            'kode_barang' => 'RK001',
            'nama_barang' => 'Sampoerna Mild 16',
            'kategori' => 'Rokok',
            'satuan' => 'bungkus',
            'harga_jual' => 32000,
            'stok' => 0,
        ]);

        // 4. Minuman
        Barang::create([
            'kode_barang' => 'MN001',
            'nama_barang' => 'Coca Cola 390ml',
            'kategori' => 'Minuman',
            'satuan' => 'botol',
            'harga_jual' => 5000,
            'stok' => 0,
        ]);
    }
}
