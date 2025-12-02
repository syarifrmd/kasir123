<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public $timestamps = true;

    public static function mapKodeToNama(): array
    {
        return static::query()->orderBy('nama')->get()->pluck('nama', 'kode')->toArray();
    }
}
