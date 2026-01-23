<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\TransaksiLedger;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiLedger::with(['barang', 'user', 'kontak']);

        $start = $request->input('start');
        $end   = $request->input('end');
        $type  = $request->input('type');

        if ($start && $end) {
            $query->whereBetween('tanggal', [$start, $end]);
        }
        
        if ($type) {
            $query->where('jenis_transaksi', $type);
        }

        $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        // Stats
        $totalMasuk = (clone $query)->where('jenis_transaksi', 'MASUK')->sum(DB::raw('qty * harga_realisasi'));
        $totalKeluar = (clone $query)->where('jenis_transaksi', 'KELUAR')->sum(DB::raw('qty * harga_realisasi'));

        $transaksi = $query->paginate(20)->withQueryString();

        return view('transaksi.index', [
            'transaksi' => $transaksi,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
        ]);
    }

    public function create()
    {
        $barang = Barang::orderBy('nama_barang')->get();
        $kategoris = Barang::select('kategori')->distinct()->pluck('kategori');
        $customers = Kontak::where('tipe', 'CUSTOMER')->get();
        
        return view('transaksi.create', compact('barang', 'customers', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'items' => 'required|array',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'metode_bayar' => 'required|string',
            'kontak_id' => 'nullable|exists:kontak,id',
            'new_customer_name' => 'nullable|string|max:255',
        ]);

        $kodeTrans = 'TRX-' . time();
        $userId = auth()->id() ?? 1;

        // Handle Customer (Existing OR New)
        $kontakId = $validated['kontak_id'] ?? null;
        if (empty($kontakId) && !empty($request->new_customer_name)) {
            // Create new customer on the fly
            $newCus = Kontak::create([
                'name' => $request->new_customer_name,
                'tipe' => 'CUSTOMER',
            ]);
            $kontakId = $newCus->id;
        }

        DB::transaction(function() use ($validated, $kodeTrans, $userId, $kontakId) {
            foreach ($validated['items'] as $item) {
                $barang = Barang::lockForUpdate()->find($item['barang_id']);
                
                $harga = $barang->harga_jual;
                
                if ($barang->stok < $item['qty']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak cukup! Sisa: {$barang->stok}");
                }

                TransaksiLedger::create([
                    'kode_transaksi' => $kodeTrans,
                    'tanggal' => $validated['tanggal'],
                    'jenis_transaksi' => 'KELUAR',
                    'barang_id' => $barang->id,
                    'kontak_id' => $kontakId,
                    'user_id' => $userId,
                    'qty' => $item['qty'],
                    'harga_realisasi' => $harga,
                    'metode_bayar' => $validated['metode_bayar'],
                    'status' => 'LUNAS',
                ]);
            }
        });

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan!');
    }
}
