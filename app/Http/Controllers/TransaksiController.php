<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Sekarang cukup with('barang'), bukan items.barang lagi
        $query = Transaksi::with('barang');

        $start = $request->input('start');
        $end   = $request->input('end');

        // Filter tanggal fleksibel
        if ($start && $end) {
            $query->whereBetween('tanggal_transaksi', [$start, $end]);
        } elseif ($start) {
            $query->whereDate('tanggal_transaksi', '>=', $start);
        } elseif ($end) {
            $query->whereDate('tanggal_transaksi', '<=', $end);
        }

        // Urutkan berdasarkan tanggal transaksi terbaru
        $query->orderBy('tanggal_transaksi', 'desc')
              ->orderBy('id', 'desc');

        $transaksi = $query->paginate(10)->withQueryString();

        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        // Sesuaikan dengan kolom yang ada di tabel barang
        $barang = Barang::orderBy('kategori')
                        ->orderBy('ukuran_kemasan')
                        ->get();

        return view('transaksi.create', compact('barang'));
    }

    public function store(Request $request)
    {
        // volume_barang = nama field di form, nanti kita mapping ke qty
        $data = $request->validate([
            'kode_barang'        => 'required|exists:barang,kode_barang',
            'volume_barang'      => 'required|integer|min:1',
            'tanggal_transaksi'  => 'required|date',
            'metode_transaksi'   => 'required|in:qris,transfer_bank,cash',
            'status_transaksi'   => 'required|in:lunas,belum_lunas',
            // Tambah: 'bayar_tunai', 'nama_customer' jika ada di form
        ]);

        // Ambil data barang
        $barang = Barang::where('kode_barang', $data['kode_barang'])->firstOrFail();

        // Cek stok
        if ($barang->stok_barang < $data['volume_barang']) {
            return back()
                ->withInput()
                ->withErrors(['volume_barang' => 'Stok barang tidak cukup']);
        }

        // Kurangi stok
        $barang->decrement('stok_barang', $data['volume_barang']);

        // Hitung qty & subtotal
        $qty          = $data['volume_barang'];
        $hargaSatuan  = $barang->harga_barang;
        $subtotal     = $hargaSatuan * $qty;

        // Simpan ke tabel transaksi baru
        Transaksi::create([
            'kode_barang'       => $data['kode_barang'],
            'qty'               => $qty,
            'tanggal_transaksi' => $data['tanggal_transaksi'],
            'metode_transaksi'  => $data['metode_transaksi'],
            'status_transaksi'  => $data['status_transaksi'],

            'harga_satuan'      => $hargaSatuan,
            'subtotal'          => $subtotal,
            'total_harga'       => $subtotal, // kalau 1 item = 1 nota

            // Kalau di form ada field ini, silakan aktifkan:
            // 'bayar_tunai'     => $request->input('bayar_tunai', 0),
            // 'kembalian'       => $request->input('kembalian', 0),
            // 'kasir'           => auth()->user()->name ?? null,
            // 'nama_customer'   => $request->input('nama_customer'),
        ]);

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function destroy(Transaksi $transaksi)
    {
        // Optional: kembalikan stok jika mau
        // if ($transaksi->barang) {
        //     $transaksi->barang->increment('stok_barang', $transaksi->qty);
        // }

        $transaksi->delete();

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }
}
