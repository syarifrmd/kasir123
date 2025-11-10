<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['kode','nama','kontak','telepon','alamat','no_kontak','nama_sales'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if (empty($model->kode)) {
                $model->kode = self::generateKode();
            }
        });
    }

    public static function generateKode(): string
    {
        // Format: VND-XXXX (incremental)
        $last = self::whereNotNull('kode')
            ->where('kode','like','VND-%')
            ->selectRaw('MAX(CAST(SUBSTRING(kode,5) AS UNSIGNED)) as max_num')
            ->value('max_num');
        $next = ($last ?? 0) + 1;
        return 'VND-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function batches()
    {
        return $this->hasMany(StockBatch::class);
    }
}
