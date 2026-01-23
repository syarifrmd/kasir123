<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kontak;
use App\Models\TransaksiLedger;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Produk & Stok Value
        $totalProduk = Barang::count();
        $totalStokValue = Barang::sum(DB::raw('stok * harga_jual'));
        
        // 2. Omset & Profit Hari Ini
        $today = now()->format('Y-m-d');
        $omsetToday = TransaksiLedger::where('jenis_transaksi', 'KELUAR')
            ->whereDate('tanggal', $today)
            ->sum(DB::raw('qty * harga_realisasi'));
            
        // 3. Stok Menipis (< 10)
        $lowStock = Barang::where('stok', '<', 10)->where('stok', '>', 0)->count();
        $outOfStock = Barang::where('stok', '<=', 0)->count();

        // 4. Recent Transaksi
        $recentTrx = TransaksiLedger::with(['user','barang'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalProduk', 
            'totalStokValue', 
            'omsetToday', 
            'lowStock', 
            'outOfStock',
            'recentTrx'
        ));
    }
}
