<?php

namespace App\Http\Controllers;

use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start', now()->startOfMonth()->toDateString());
        $end = $request->input('end', now()->toDateString());

        $rows = TransaksiItem::select(
            DB::raw('date(created_at) as tanggal'),
            DB::raw('sum(subtotal) as penjualan'),
            DB::raw('sum(hpp) as hpp'),
            DB::raw('sum(profit) as profit'),
            DB::raw('count(*) as item_count')
        )
        ->whereBetween(DB::raw('date(created_at)'), [$start, $end])
        ->groupBy(DB::raw('date(created_at)'))
        ->orderBy('tanggal')
        ->get();

        return view('profit.index', compact('rows','start','end'));
    }
}
