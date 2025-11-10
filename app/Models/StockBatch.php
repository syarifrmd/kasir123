<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id','vendor_id','qty_received','qty_remaining','unit_cost','sell_price_at_receive','received_at','notes'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'sell_price_at_receive' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    public function barang(){ return $this->belongsTo(Barang::class); }
    public function vendor(){ return $this->belongsTo(Vendor::class); }
}
