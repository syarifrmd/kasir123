<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id','vendor_id','type','qty','before_stock','after_stock','unit_cost','unit_price','stock_batch_id','transaksi_item_id','notes'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function barang(){ return $this->belongsTo(Barang::class); }
    public function vendor(){ return $this->belongsTo(Vendor::class); }
    public function batch(){ return $this->belongsTo(StockBatch::class, 'stock_batch_id'); }
    public function transaksiItem(){ return $this->belongsTo(TransaksiItem::class); }
}
