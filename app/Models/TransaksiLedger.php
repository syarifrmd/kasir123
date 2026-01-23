<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiLedger extends Model
{
    use HasFactory;

    protected $table = 'transaksi_ledger';

    protected $fillable = [
        'kode_transaksi',
        'tanggal',
        'jenis_transaksi', // MASUK, KELUAR
        'barang_id',
        'kontak_id',
        'user_id',
        'qty',
        'harga_realisasi',
        'expired_date',
        'metode_bayar',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'qty' => 'decimal:3',
        'harga_realisasi' => 'decimal:2',
        'expired_date' => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function kontak()
    {
        return $this->belongsTo(Kontak::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
