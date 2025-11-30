<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    public function index()
    {
        // pastikan tabel barang punya kolom kategori, merk, jenis
        $barang = Barang::where('stok_barang', '>', 0)
            ->orderBy('kategori')
            ->orderBy('merk')
            ->orderBy('jenis')
            ->get();
        
        $barangGrouped = $barang->groupBy(function($item) {
            return $item->kategori . '|' . $item->merk;
        })->map(function($group) {
            $first = $group->first();
            return [
                'kategori'      => $first->kategori,
                'merk'          => $first->merk,
                'kode_kategori' => $first->kategori,
                'varians'       => $group->values()->toArray(),
            ];
        })->values();
        
        return view('pos.index', compact('barangGrouped'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items'                 => 'required|array|min:1',
            'items.*.barang_id'     => 'required|exists:barang,id',
            'items.*.qty'           => 'required|integer|min:1',
            'metode_transaksi'      => 'required|in:qris,transfer_bank,cash',
            'status_transaksi'      => 'required|in:lunas,belum_lunas',
            'bayar_tunai'           => 'nullable|numeric|min:0',
            'nama_customer'         => 'nullable|string|max:100',
        ]);

        $tanggal     = now()->toDateString();
        $totalHarga  = 0;
        $itemsData   = [];

        // Buat kode_transaksi sekali per nota
        $kodeTransaksi = 'TRX-' . now()->format('YmdHis');
        $kasirName     = Auth::user()->name ?? 'Kasir';

        DB::transaction(function () use (&$totalHarga, &$itemsData, $data, $tanggal, $kodeTransaksi, $kasirName) {

            foreach ($data['items'] as $item) {
                /** @var Barang $barang */
                $barang = Barang::lockForUpdate()->find($item['barang_id']);

                if (!$barang) {
                    abort(422, "Barang tidak ditemukan");
                }

                if ($barang->stok_barang < $item['qty']) {
                    abort(422, "Stok {$barang->merk} {$barang->jenis} tidak cukup (tersedia: {$barang->stok_barang})");
                }

                $qty       = (int) $item['qty'];
                $subtotal  = $barang->harga_barang * $qty;
                $totalHarga += $subtotal;

                // Kurangi stok barang
                $barang->decrement('stok_barang', $qty);

                // Simpan ke tabel transaksi (1 baris per item)
                Transaksi::create([
                    'kode_transaksi'    => $kodeTransaksi,
                    'tanggal_transaksi' => $tanggal,
                    'kode_barang'       => $barang->kode_barang,
                    'qty'               => $qty,
                    'harga_satuan'      => $barang->harga_barang,
                    'subtotal'          => $subtotal,
                    // total_harga & kembalian akan di-update setelah loop
                    'total_harga'       => 0,
                    'bayar_tunai'       => $data['bayar_tunai'] ?? 0,
                    'kembalian'         => 0,
                    'metode_transaksi'  => $data['metode_transaksi'],
                    'status_transaksi'  => $data['status_transaksi'],
                    'kasir'             => $kasirName,
                    'nama_customer'     => $data['nama_customer'] ?? null,
                ]);

                $itemsData[] = [
                    'nama'      => $barang->merk . ' ' . $barang->jenis,
                    'ukuran'    => $barang->ukuran_kemasan,
                    'qty'       => $qty,
                    'harga'     => $barang->harga_barang,
                    'subtotal'  => $subtotal,
                ];
            }

            // Hitung kembalian & update semua baris transaksi dengan kode_transaksi ini
            $kembalian = max(0, ($data['bayar_tunai'] ?? 0) - $totalHarga);

            Transaksi::where('kode_transaksi', $kodeTransaksi)
                ->update([
                    'total_harga' => $totalHarga,
                    'kembalian'   => $kembalian,
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan',
            'data'    => [
                'kode_transaksi' => $kodeTransaksi,
                'jumlah_item'    => count($itemsData),
                'total_harga'    => $totalHarga,
                'bayar_tunai'    => $data['bayar_tunai'] ?? 0,
                'kembalian'      => max(0, ($data['bayar_tunai'] ?? 0) - $totalHarga),
                'items'          => $itemsData,
            ],
        ]);
    }

    public function nota($id)
    {
        // Anggap $id = id salah satu baris transaksi
        $transaksiUtama = Transaksi::findOrFail($id);

        $items = Transaksi::with('barang')
            ->where('kode_transaksi', $transaksiUtama->kode_transaksi)
            ->get();

        $totalHarga = $transaksiUtama->total_harga;
        $bayar      = $transaksiUtama->bayar_tunai;
        $kembalian  = $transaksiUtama->kembalian;

        return view('pos.nota', [
            'transaksi'   => $transaksiUtama,
            'items'       => $items,
            'totalHarga'  => $totalHarga,
            'bayar'       => $bayar,
            'kembalian'   => $kembalian,
        ]);
    }
}
