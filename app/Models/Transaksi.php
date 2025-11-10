<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'tanggal_transaksi',
        'total_harga',
        'bayar_tunai',
        'kembalian',
        'metode_transaksi',
        'status_transaksi',
        'kasir',
        'nama_customer',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'total_harga' => 'decimal:2',
        'bayar_tunai' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $trx) {
            if (empty($trx->kode_transaksi)) {
                // Format: TRX-YYYYMMDD-XXXX
                $prefix = 'TRX-' . now()->format('Ymd') . '-';
                $latest = static::where('kode_transaksi', 'like', $prefix . '%')
                    ->orderByDesc('kode_transaksi')
                    ->first();
                $next = 1;
                if ($latest) {
                    $number = (int) Str::afterLast($latest->kode_transaksi, '-');
                    $next = $number + 1;
                }
                $trx->kode_transaksi = $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }

    // Get all barang through items relationship
    public function getBarangAttribute()
    {
        return $this->items->map(function($item) {
            return $item->barang;
        })->filter()->unique('id');
    }
}
