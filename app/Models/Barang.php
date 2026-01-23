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
        'nama_barang',
        'merk',
        'ukuran',
        'kategori',
        'satuan',
        'harga_jual',
        'stok',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'stok' => 'decimal:3',
    ];

    public function history()
    {
        return $this->hasMany(TransaksiLedger::class);
    }
}
