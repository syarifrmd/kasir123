<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'kontak';

    protected $fillable = [
        'name',
        'tipe', // VENDOR, CUSTOMER
        'hp',
        'alamat',
    ];

    public function scopeVendor($query)
    {
        return $query->where('tipe', 'VENDOR');
    }

    public function scopeCustomer($query)
    {
        return $query->where('tipe', 'CUSTOMER');
    }
}
