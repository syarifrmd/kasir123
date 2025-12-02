<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan query dasar tanpa eager load relasi yang tidak tersedia
        $query = Transaksi::query();

        $start = $request->input('start');
        $end   = $request->input('end');
        $period = $request->input('period'); // harian|mingguan|bulanan|tahunan

        // Quick period filter if provided
        if (!$start && !$end && $period) {
            $today = Carbon::today();
            switch ($period) {
                case 'harian':
                    $start = $today->toDateString();
                    $end   = $today->toDateString();
                    break;
                case 'mingguan':
                    $start = $today->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
                    $end   = $today->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();
                    break;
                case 'bulanan':
                    $start = $today->copy()->startOfMonth()->toDateString();
                    $end   = $today->copy()->endOfMonth()->toDateString();
                    break;
                case 'tahunan':
                    $start = $today->copy()->startOfYear()->toDateString();
                    $end   = $today->copy()->endOfYear()->toDateString();
                    break;
            }
        }

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

        // Hitung ringkasan sebelum pagination
        $totalOmset = $query->sum('total_harga');
        $totalTransaksi = $query->count();

        $transaksi = $query->paginate(10)->withQueryString();

        // Siapkan daftar items per kode_transaksi dari tabel transaksi legacy (satu baris per item)
        $codes = $transaksi->getCollection()->pluck('kode_transaksi')->unique()->values()->all();
        $itemsByTrx = collect();
        if (!empty($codes)) {
            $rows = Transaksi::select(
                    'transaksi.kode_transaksi',
                    'transaksi.kode_barang',
                    'transaksi.qty',
                    'transaksi.harga_satuan',
                    'transaksi.subtotal',
                    'barang.merk',
                    'barang.jenis'
                )
                ->leftJoin('barang','barang.kode_barang','=','transaksi.kode_barang')
                ->whereIn('transaksi.kode_transaksi', $codes)
                ->orderBy('transaksi.id')
                ->get()
                ->map(function($item) {
                    $item->nama_display = trim(($item->merk ?? '') . ' ' . ($item->jenis ?? ''));
                    return $item;
                })
                ->groupBy('kode_transaksi');
            $itemsByTrx = $rows;
        }

        return view('transaksi.index', [
            'transaksi' => $transaksi,
            'itemsByTrx' => $itemsByTrx,
            'totalOmset' => $totalOmset,
            'totalTransaksi' => $totalTransaksi,
        ]);
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
