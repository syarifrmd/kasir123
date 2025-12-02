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

    public static function kategoriOptions(): array
    {
        $defaults = self::KATEGORI;
        $path = storage_path('app/kategori.json');
        if (is_file($path)) {
            try {
                $json = json_decode(file_get_contents($path), true) ?: [];
                if (is_array($json)) {
                    // Expect associative [kode => nama]
                    foreach ($json as $k => $v) {
                        if (is_string($k) && is_string($v) && preg_match('/^[A-Z]{2}$/', $k)) {
                            $defaults[$k] = $v;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // ignore and use defaults
            }
        }
        ksort($defaults);
        return $defaults;
    }

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
