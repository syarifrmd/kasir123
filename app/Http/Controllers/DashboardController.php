<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\StockBatch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date range filter (default: last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // KPI Cards
        $totalRevenue = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('total_harga');

        $totalProfit = TransaksiItem::whereHas('transaksi', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })
            ->sum('profit');

        $totalTransactions = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])->count();

        $totalStockValue = Barang::sum(DB::raw('stok_barang * harga_barang'));

        // Low Stock Alert (stok < 10)
        $lowStock = Barang::where('stok_barang', '<', 10)->where('stok_barang', '>', 0)->count();
        $outOfStock = Barang::where('stok_barang', '<=', 0)->count();

        // Profit Trend (daily for selected period)
        $profitTrend = TransaksiItem::select(
                DB::raw('DATE(transaksi.tanggal_transaksi) as date'),
                DB::raw('SUM(transaksi_items.profit) as total_profit'),
                DB::raw('SUM(transaksi_items.subtotal) as total_revenue')
            )
            ->join('transaksi', 'transaksi_items.transaksi_id', '=', 'transaksi.id')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Stock by Category
        $stockByCategory = Barang::select(
                'kategori',
                DB::raw('SUM(stok_barang) as total_stok'),
                DB::raw('COUNT(*) as total_items'),
                DB::raw('SUM(stok_barang * harga_barang) as total_value')
            )
            ->groupBy('kategori')
            ->get();

        // Revenue by Category
        $revenueByCategory = TransaksiItem::select(
                'barang.kategori',
                DB::raw('SUM(transaksi_items.subtotal) as total_revenue'),
                DB::raw('SUM(transaksi_items.profit) as total_profit')
            )
            ->join('barang', 'transaksi_items.barang_id', '=', 'barang.id')
            ->join('transaksi', 'transaksi_items.transaksi_id', '=', 'transaksi.id')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->groupBy('barang.kategori')
            ->get();

        // Top Products (by revenue)
        $topProducts = TransaksiItem::select(
                'barang.id',
                'barang.merk',
                'barang.jenis',
                'barang.kategori',
                DB::raw('SUM(transaksi_items.qty) as total_qty'),
                DB::raw('SUM(transaksi_items.subtotal) as total_revenue'),
                DB::raw('SUM(transaksi_items.profit) as total_profit')
            )
            ->join('barang', 'transaksi_items.barang_id', '=', 'barang.id')
            ->join('transaksi', 'transaksi_items.transaksi_id', '=', 'transaksi.id')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->groupBy('barang.id', 'barang.merk', 'barang.jenis', 'barang.kategori')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Recent Stock Movements
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
