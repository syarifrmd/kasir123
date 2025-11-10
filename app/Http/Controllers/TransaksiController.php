<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
    $transaksi = Transaksi::with(['items.barang'])->latest()->paginate(10);
        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        $barang = Barang::orderBy('merk')->orderBy('jenis')->get();
        return view('transaksi.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_barang' => 'required|exists:barang,kode_barang',
            'volume_barang' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'metode_transaksi' => 'required|in:qris,transfer_bank,cash',
            'status_transaksi' => 'required|in:lunas,belum_lunas',
        ]);

        // Kurangi stok
        $barang = Barang::where('kode_barang', $data['kode_barang'])->first();
        if ($barang->stok_barang < $data['volume_barang']) {
            return back()->withInput()->withErrors(['volume_barang' => 'Stok barang tidak cukup']);
        }
        $barang->decrement('stok_barang', $data['volume_barang']);

        Transaksi::create($data); // kode otomatis
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function destroy(Transaksi $transaksi)
    {
        // Optional: kembalikan stok jika ingin
        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus');
    }
}
