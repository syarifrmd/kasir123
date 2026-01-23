<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiLedger;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start', now()->startOfMonth()->toDateString());
        $end   = $request->input('end', now()->endOfMonth()->toDateString());

        // Simple Cash Flow Method: Sales - COGS is hard because we don't track COGS per item strictly in simple ledger without FIFO table.
        // But we can approximate or just show Cash IN vs Cash OUT.
        
        // Cash IN (Sales)
        $omset = TransaksiLedger::where('jenis_transaksi', 'KELUAR')
            ->whereBetween('tanggal', [$start, $end])
            ->sum(DB::raw('qty * harga_realisasi'));

        // Cash OUT (Restock Purchases)
        $pembelian = TransaksiLedger::where('jenis_transaksi', 'MASUK')
            ->whereBetween('tanggal', [$start, $end])
            ->sum(DB::raw('qty * harga_realisasi'));

        $grossProfit = $omset - $pembelian; 

        return view('profit.index', compact('omset', 'pembelian', 'grossProfit', 'start', 'end'));
    }
}
