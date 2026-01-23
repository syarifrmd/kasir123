<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

// This controller now just redirects to the main Stock/Barang page or handles specific stock views.
// Or we can alias it to Transaksi MASUK.
class StokController extends Controller
{
    public function index()
    {
        return redirect()->route('barang.index');
    }

    public function create()
    {
        // "Tambah Stok" now means creating a MASUK transaction
        // We can redirect to a pre-filled transaction create page or a specific stock-in page.
        // For simplicity, let's redirect to transaction create but with type=MASUK implied if we built it.
        // Since my TransaksiController handles Sales (KELUAR) mostly visually, I should probably add a "Restock" mode.
        return redirect()->route('transaksi.create', ['mode' => 'masuk']);
    }
}
