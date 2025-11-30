<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Total revenue dari subtotal
        $totalRevenue = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('subtotal');

        // Sementara: profit = totalRevenue (tidak ada HPP detail)
        $totalProfit = $totalRevenue;

        // Total transaksi (nota) = jumlah kode_transaksi unik
        $totalTransactions = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->distinct('kode_transaksi')
            ->count('kode_transaksi');

        // Total nilai stok
        $totalStockValue = Barang::sum(DB::raw('stok_barang * harga_barang'));

        $lowStock  = Barang::where('stok_barang', '<', 10)->where('stok_barang', '>', 0)->count();
        $outOfStock= Barang::where('stok_barang', '<=', 0)->count();

        // Profit trend per hari
        $profitTrend = Transaksi::select(
                DB::raw('DATE(tanggal_transaksi) as date'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(subtotal) as total_profit')
            )
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Stock by category
        $stockByCategory = Barang::select(
                'kategori',
                DB::raw('SUM(stok_barang) as total_stok'),
                DB::raw('COUNT(*) as total_items'),
                DB::raw('SUM(stok_barang * harga_barang) as total_value')
            )
            ->groupBy('kategori')
            ->get();

        // Revenue by category
        $revenueByCategory = Transaksi::select(
                'barang.kategori',
                DB::raw('SUM(transaksi.subtotal) as total_revenue'),
                DB::raw('SUM(transaksi.subtotal) as total_profit')
            )
            ->join('barang', 'transaksi.kode_barang', '=', 'barang.kode_barang')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->groupBy('barang.kategori')
            ->get();

        // Top products
        $topProducts = Transaksi::select(
                'barang.id',
                'barang.merk',
                'barang.jenis',
                'barang.kategori',
                DB::raw('SUM(transaksi.qty) as total_qty'),
                DB::raw('SUM(transaksi.subtotal) as total_revenue'),
                DB::raw('SUM(transaksi.subtotal) as total_profit')
            )
            ->join('barang', 'transaksi.kode_barang', '=', 'barang.kode_barang')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->groupBy('barang.id', 'barang.merk', 'barang.jenis', 'barang.kategori')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $recentMovements = StockMovement::with(['barang', 'vendor'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalRevenue',
            'totalProfit',
            'totalTransactions',
            'totalStockValue',
            'lowStock',
            'outOfStock',
            'profitTrend',
            'stockByCategory',
            'revenueByCategory',
            'topProducts',
            'recentMovements',
            'startDate',
            'endDate'
        ));
    }
}
