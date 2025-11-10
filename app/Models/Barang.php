<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'kategori',
        'merk',
        'jenis',
        'ukuran_kemasan',
        'harga_barang',
        'stok_barang',
        'deskripsi',
    ];

    // Kategori constants
    public const KATEGORI = [
        'RK' => 'Rokok',
        'MN' => 'Minuman',
        'SB' => 'Sembako',
        'SK' => 'Snack',
        'OB' => 'Obat',
        'EK' => 'Elektronik',
    ];

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Flattened model: no details relation
    // kode_barang generation handled in controller
}
