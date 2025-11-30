<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitController extends Controller
{
    public function index(Request $request)
{
    $start = $request->input('start', now()->startOfMonth()->toDateString());
    $end   = $request->input('end', now()->toDateString());

    $rows = Transaksi::select(
            DB::raw('DATE(tanggal_transaksi) as tanggal'),
            DB::raw('SUM(subtotal) as penjualan'),
            DB::raw('0 as hpp'),
            DB::raw('SUM(subtotal) as profit'),
            DB::raw('COUNT(*) as item_count')
        )
        ->whereBetween('tanggal_transaksi', [$start, $end])
        ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
        ->orderBy('tanggal')
        ->get();

    return view('profit.index', compact('rows','start','end'));
}

}
