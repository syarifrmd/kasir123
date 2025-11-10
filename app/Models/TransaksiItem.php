<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $fillable = [
        'transaksi_id',
        'barang_id',
        'qty',
        'harga_satuan',
        'subtotal',
        'hpp',
        'profit',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'hpp' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
