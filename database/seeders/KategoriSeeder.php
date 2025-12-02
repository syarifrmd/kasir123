<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['kode' => 'RK', 'nama' => 'Rokok'],
            ['kode' => 'MN', 'nama' => 'Minuman'],
            ['kode' => 'SB', 'nama' => 'Sembako'],
            ['kode' => 'SK', 'nama' => 'Snack'],
            ['kode' => 'OB', 'nama' => 'Obat'],
            ['kode' => 'EK', 'nama' => 'Elektronik'],
        ];
        foreach ($items as $it) {
            Kategori::updateOrCreate(['kode' => $it['kode']], ['nama' => $it['nama']]);
        }
    }
}
