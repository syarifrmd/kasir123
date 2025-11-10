<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Vendor;
use App\Models\StockBatch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        // Search by barang name/kode
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($w) use ($q) {
                $w->where('merk', 'like', "%$q%")
                  ->orWhere('jenis', 'like', "%$q%")
                  ->orWhere('kode_barang', 'like', "%$q%")
                  ->orWhere('ukuran_kemasan', 'like', "%$q%");
            });
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Availability filter
        if ($request->filled('available')) {
            if ($request->available === 'ada') {
                $query->where('stok_barang', '>', 0);
            } elseif ($request->available === 'habis') {
                $query->where('stok_barang', '<=', 0);
            }
        }

        // Filter by vendor via stock batches
        if ($request->filled('vendor_id')) {
            $vendorId = (int) $request->vendor_id;
            $query->whereExists(function($sub) use ($vendorId) {
                $sub->select(DB::raw(1))
                    ->from('stock_batches as sb')
                    ->whereColumn('sb.barang_id', 'barang.id')
                    ->where('sb.vendor_id', $vendorId);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'nama');
        if ($sort === 'terbaru_masuk') {
            $batchAgg = DB::table('stock_batches')
                ->select('barang_id', DB::raw('MAX(received_at) as last_in'))
                ->groupBy('barang_id');
            $query->leftJoinSub($batchAgg, 'ba', function($join) {
                $join->on('ba.barang_id', '=', 'barang.id');
            });
            $query->orderByDesc('ba.last_in')
                  ->orderBy('merk')->orderBy('jenis');
        } elseif ($sort === 'terlama_masuk') {
            $batchAgg = DB::table('stock_batches')
                ->select('barang_id', DB::raw('MIN(received_at) as first_in'))
                ->groupBy('barang_id');
            $query->leftJoinSub($batchAgg, 'ba', function($join) {
                $join->on('ba.barang_id', '=', 'barang.id');
            });
            $query->orderBy('ba.first_in')
                  ->orderBy('merk')->orderBy('jenis');
        } elseif ($sort === 'stok_banyak') {
            $query->orderByDesc('stok_barang')->orderBy('merk')->orderBy('jenis');
        } elseif ($sort === 'stok_sedikit') {
            $query->orderBy('stok_barang')->orderBy('merk')->orderBy('jenis');
        } else {
            $query->orderBy('merk')->orderBy('jenis');
        }

        // Pagination
        $perPage = (int) ($request->input('per_page', 20));
        $perPage = $perPage > 0 && $perPage <= 200 ? $perPage : 20;
        
        $totalCount = $query->count();
        
        $barang = $query
            ->withCount(['stockBatches as total_batches' => function($q){},])
            ->withSum('stockBatches as qty_tersisa', 'qty_remaining')
            ->paginate($perPage)
            ->withQueryString();

        $vendors = Vendor::orderBy('nama')->get(['id', 'nama']);

        return view('stok.index', compact('barang', 'vendors', 'totalCount'));
    }

    public function create()
    {
        $barang = Barang::orderBy('merk')->orderBy('jenis')->get();
        $vendors = Vendor::orderBy('nama')->get();
        return view('stok.create', compact('barang','vendors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'vendor_baru' => 'nullable|string|max:100',
            'vendor_baru_kode' => 'nullable|string|max:20',
            'vendor_baru_alamat' => 'nullable|string|max:255',
            'vendor_baru_no_kontak' => 'nullable|string|max:30',
            'vendor_baru_nama_sales' => 'nullable|string|max:100',
            'qty' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'harga_jual_baru' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($data) {
            $barang = Barang::lockForUpdate()->findOrFail($data['barang_id']);

            // Create vendor if provided as new
            $vendorId = $data['vendor_id'] ?? null;
            if (!$vendorId && !empty($data['vendor_baru'])) {
                $vendor = Vendor::create([
                    'kode' => $data['vendor_baru_kode'] ?? null,
                    'nama' => $data['vendor_baru'],
                    'alamat' => $data['vendor_baru_alamat'] ?? null,
                    'no_kontak' => $data['vendor_baru_no_kontak'] ?? null,
                    'nama_sales' => $data['vendor_baru_nama_sales'] ?? null,
                ]);
                $vendorId = $vendor->id;
            }

            $before = $barang->stok_barang;
            // Create batch
            $batch = StockBatch::create([
                'barang_id' => $barang->id,
                'vendor_id' => $vendorId,
                'qty_received' => $data['qty'],
                'qty_remaining' => $data['qty'],
                'unit_cost' => $data['unit_cost'],
                'sell_price_at_receive' => $data['harga_jual_baru'] ?? $barang->harga_barang,
                'notes' => $data['notes'] ?? null,
            ]);

            // Update barang stock and optionally price
            $barang->stok_barang = $before + $data['qty'];
            if (!empty($data['harga_jual_baru'])) {
                $barang->harga_barang = $data['harga_jual_baru'];
            }
            $barang->save();

            // Movement record
            StockMovement::create([
                'barang_id' => $barang->id,
                'vendor_id' => $vendorId,
                'type' => 'in',
                'qty' => $data['qty'],
                'before_stock' => $before,
                'after_stock' => $barang->stok_barang,
                'unit_cost' => $data['unit_cost'],
                'unit_price' => $barang->harga_barang,
                'stock_batch_id' => $batch->id,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('stok.index')->with('success','Stok berhasil ditambahkan');
    }

    public function show(Barang $barang)
    {
        $barang->load(['stockBatches.vendor' => function($q){}, 'stockMovements' => function($q){ $q->latest(); }]);
        $batches = $barang->stockBatches()->with('vendor')->orderBy('received_at')->get();
        $movements = $barang->stockMovements()->with(['vendor','batch'])->latest()->paginate(50);
        return view('stok.show', compact('barang','batches','movements'));
    }
}
