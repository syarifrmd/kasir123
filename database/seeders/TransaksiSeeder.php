<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kontak;
use App\Models\TransaksiLedger;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $vendor = Kontak::where('tipe', 'VENDOR')->first();
        $barang1 = Barang::where('kode_barang', 'SB001')->first(); // Gula
        $barang2 = Barang::where('kode_barang', 'RK001')->first(); // Rokok

        if (!$barang1 || !$barang2) {
             return;
        }

        // 1. Restock (MASUK) - Gula 100kg @ 14000
        TransaksiLedger::create([
            'kode_transaksi' => 'IN-001',
            'tanggal' => now()->subDays(2),
            'jenis_transaksi' => 'MASUK',
            'barang_id' => $barang1->id,
            'kontak_id' => $vendor ? $vendor->id : null,
            'user_id' => $admin->id,
            'qty' => 100,
            'harga_realisasi' => 14000,
            'status' => 'LUNAS',
        ]);
        // Trigger should update Stock Gula -> 100

        // 2. Restock (MASUK) - Rokok 50 Bungkus @ 28000
        TransaksiLedger::create([
            'kode_transaksi' => 'IN-002',
            'tanggal' => now()->subDays(2),
            'jenis_transaksi' => 'MASUK',
            'barang_id' => $barang2->id,
            'kontak_id' => $vendor ? $vendor->id : null,
            'user_id' => $admin->id,
            'qty' => 50,
            'harga_realisasi' => 28000,
            'status' => 'LUNAS',
        ]);
        // Trigger should update Stock Rokok -> 50

        // 3. Sales (KELUAR) - Gula 2.5 kg (Eceran Test) @ 16000
        TransaksiLedger::create([
            'kode_transaksi' => 'TRX-001',
            'tanggal' => now()->subDay(),
            'jenis_transaksi' => 'KELUAR',
            'barang_id' => $barang1->id, // Gula
            'kontak_id' => null, // Walk-in customer
            'user_id' => $admin->id,
            'qty' => 2.5,
            'harga_realisasi' => 16000, // Harga jual
            'metode_bayar' => 'CASH',
            'status' => 'LUNAS',
        ]);
        // Trigger should update Stock Gula -> 100 - 2.5 = 97.5

        // 4. Sales (KELUAR) - Rokok 2 bungkus
        TransaksiLedger::create([
            'kode_transaksi' => 'TRX-002',
            'tanggal' => now(),
            'jenis_transaksi' => 'KELUAR',
            'barang_id' => $barang2->id,
            'kontak_id' => null,
            'user_id' => $admin->id,
            'qty' => 2,
            'harga_realisasi' => 32000,
            'metode_bayar' => 'QRIS',
            'status' => 'LUNAS',
        ]);
        // Trigger should update Stock Rokok -> 50 - 2 = 48
    }
}
