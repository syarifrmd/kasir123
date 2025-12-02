<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\StockBatch;
use App\Models\StockMovement;
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
            'items.*.type'          => 'required|in:stock,manual',
            'items.*.barang_id'     => 'nullable|exists:barang,id',
            'items.*.nama_manual'   => 'nullable|string|max:150',
            'items.*.harga_manual'  => 'nullable|numeric|min:0',
            'items.*.qty'           => 'required|numeric|min:0.001',
            'items.*.kode_barang'   => 'nullable|string',
            'metode_transaksi'      => 'required|in:qris,transfer_bank,cash',
            'status_transaksi'      => 'required|in:lunas,belum_lunas',
            'bayar_tunai'           => 'nullable|numeric|min:0',
            'nama_customer'         => 'nullable|string|max:100',
            'tanggal_transaksi'     => 'nullable|date',
        ]);

        $tanggal     = $request->input('tanggal_transaksi', now()->toDateString());
        $totalHarga  = 0;
        $itemsData   = [];
        $firstTransaksiId = null;

        // Buat kode_transaksi sekali per nota
        $kodeTransaksi = 'TRX-' . now()->format('YmdHis');
        $kasirName     = Auth::user()->name ?? 'Kasir';

        DB::transaction(function () use (&$totalHarga, &$itemsData, $data, $tanggal, $kodeTransaksi, $kasirName, &$firstTransaksiId) {

            foreach ($data['items'] as $item) {
                $qty = (float) $item['qty'];

                /** @var Barang $barang */
                $barang = Barang::lockForUpdate()->find($item['barang_id']);

                if (!$barang) {
                    abort(422, "Barang tidak ditemukan");
                }

                $hargaSatuan = $barang->harga_barang;
                $subtotal    = $hargaSatuan * $qty;
                $totalHarga += $subtotal;

                if ($item['type'] === 'stock') {
                    // Item stok: cek & kurangi stok
                    if ($barang->stok_barang < $qty) {
                        abort(422, "Stok {$barang->merk} {$barang->jenis} tidak cukup (tersedia: {$barang->stok_barang})");
                    }

                    // Kurangi stok barang (kolom agregat)
                    $beforeStock = $barang->stok_barang;
                    $barang->stok_barang = max(0, $barang->stok_barang - $qty);
                    $barang->save();

                    // Kurangi stok per batch (FIFO)
                    $remaining = $qty;
                    $batches = StockBatch::where('barang_id', $barang->id)
                        ->where('qty_remaining', '>', 0)
                        ->orderBy('received_at')
                        ->lockForUpdate()
                        ->get();

                    foreach ($batches as $batch) {
                        if ($remaining <= 0) break;
                        $take = min($remaining, $batch->qty_remaining);

                        $batch->qty_remaining -= $take;
                        $batch->save();

                        // Catat movement keluar per batch
                        StockMovement::create([
                            'barang_id'      => $barang->id,
                            'vendor_id'      => $batch->vendor_id,
                            'type'           => 'out',
                            'qty'            => $take,
                            'before_stock'   => $beforeStock,
                            'after_stock'    => max(0, $beforeStock - $qty),
                            'unit_cost'      => $batch->unit_cost,
                            'unit_price'     => $hargaSatuan,
                            'stock_batch_id' => $batch->id,
                            'notes'          => 'Penjualan via kasir ' . $kodeTransaksi,
                        ]);

                        $remaining -= $take;
                    }

                    $kodeBarang = $item['kode_barang'] ?? $barang->kode_barang;
                    $namaTampil = $barang->merk . ' ' . $barang->jenis;
                } else {
                    // manual item: tidak mengurangi stok, tapi tetap pakai barang_id untuk ambil nama
                    $kodeBarang = $item['kode_barang'] ?? $barang->kode_barang;
                    $namaTampil = $barang->merk . ' ' . $barang->jenis . ' (parsial)';
                }

                // Simpan ke tabel transaksi (1 baris per item)
                $created = Transaksi::create([
                    'kode_transaksi'    => $kodeTransaksi,
                    'tanggal_transaksi' => $tanggal,
                    'kode_barang'       => $kodeBarang,
                    'qty'               => $qty,
                    'harga_satuan'      => $hargaSatuan,
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

                if ($firstTransaksiId === null) {
                    $firstTransaksiId = $created->id;
                }

                $itemsData[] = [
                    'nama'      => $namaTampil,
                    'ukuran'    => $item['type'] === 'stock' ? ($barang->ukuran_kemasan ?? '') : '',
                    'qty'       => $qty,
                    'harga'     => $hargaSatuan,
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
                'transaksi_id'   => $firstTransaksiId,
            ],
        ]);
    }

    public function nota($id)
    {
        // Anggap $id = id salah satu baris transaksi
        $transaksiUtama = Transaksi::findOrFail($id);

        $items = Transaksi::select(
                'transaksi.*',
                'barang.merk',
                'barang.jenis',
                'barang.ukuran_kemasan'
            )
            ->join('barang','barang.kode_barang','=','transaksi.kode_barang')
            ->where('transaksi.kode_transaksi', $transaksiUtama->kode_transaksi)
            ->orderBy('transaksi.id')
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
