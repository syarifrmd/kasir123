<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\StockBatch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $barang = Barang::where('stok_barang', '>', 0)->orderBy('kategori')->orderBy('merk')->orderBy('jenis')->get();
        
        // Group by kategori + merk
        $barangGrouped = $barang->groupBy(function($item) {
            return $item->kategori . '|' . $item->merk;
        })->map(function($group) {
            $first = $group->first();
            return [
                'kategori' => $first->kategori,
                'merk' => $first->merk,
                'kode_kategori' => $first->kategori,
                'varians' => $group->values()->toArray()
            ];
        })->values();
        
        return view('pos.index', compact('barangGrouped'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.qty' => 'required|integer|min:1',
            'metode_transaksi' => 'required|in:qris,transfer_bank,cash',
            'status_transaksi' => 'required|in:lunas,belum_lunas',
            'bayar_tunai' => 'nullable|numeric|min:0',
            'nama_customer' => 'nullable|string|max:100',
        ]);

        $tanggal = now()->toDateString();
        $totalHarga = 0;
        $transaksi = null;
        $itemsData = [];

        DB::transaction(function () use (&$transaksi, &$totalHarga, &$itemsData, $data, $tanggal) {
            // Create transaksi header
            $transaksi = Transaksi::create([
                'tanggal_transaksi' => $tanggal,
                'total_harga' => 0, 
                'bayar_tunai' => $data['bayar_tunai'] ?? 0,
                'kembalian' => 0, 
                'metode_transaksi' => $data['metode_transaksi'],
                'status_transaksi' => $data['status_transaksi'],
                'kasir' => auth()->user()->name ?? 'Kasir',
                'nama_customer' => $data['nama_customer'] ?? null,
            ]);
            
            // Process each item
            foreach ($data['items'] as $item) {
                /** @var Barang $barang */
                $barang = Barang::lockForUpdate()->find($item['barang_id']);
                
                if (!$barang) {
                    abort(422, "Barang tidak ditemukan");
                }
                
                if ($barang->stok_barang < $item['qty']) {
                    abort(422, "Stok {$barang->merk} {$barang->jenis} tidak cukup (tersedia: {$barang->stok_barang})");
                }
                
                // Calculate subtotal
                $subtotal = $barang->harga_barang * $item['qty'];
                $totalHarga += $subtotal;
                
                // Create transaksi item (hpp & profit will be updated after FIFO allocation)
                $transaksiItem = TransaksiItem::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barang->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $barang->harga_barang,
                    'subtotal' => $subtotal,
                ]);

                // FIFO allocate from stock batches to compute HPP and create movement(s)
                $remain = (int) $item['qty'];
                $cogsTotal = 0.0;
                $currentStock = $barang->stok_barang; // before deduction

                $batches = StockBatch::where('barang_id', $barang->id)
                    ->where('qty_remaining', '>', 0)
                    ->orderBy('received_at')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {
                    if ($remain <= 0) break;
                    $take = min($batch->qty_remaining, $remain);
                    if ($take <= 0) continue;

                    // Update batch remaining
                    $batch->qty_remaining -= $take;
                    $batch->save();

                    // Accumulate COGS
                    $cogsTotal += $take * (float)$batch->unit_cost;

                    // Record movement OUT per batch
                    $before = $currentStock;
                    $after = $currentStock - $take;
                    StockMovement::create([
                        'barang_id' => $barang->id,
                        'vendor_id' => $batch->vendor_id,
                        'type' => 'out',
                        'qty' => $take,
                        'before_stock' => $before,
                        'after_stock' => $after,
                        'unit_cost' => $batch->unit_cost,
                        'unit_price' => $barang->harga_barang,
                        'stock_batch_id' => $batch->id,
                        'transaksi_item_id' => $transaksiItem->id,
                        'notes' => 'Penjualan',
                    ]);

                    // Update counters
                    $currentStock = $after;
                    $remain -= $take;
                }

                // As a fallback if no batches found, assume zero cost
                $hpp = (float) $cogsTotal;
                $profit = (float) $subtotal - $hpp;
                $transaksiItem->update(['hpp' => $hpp, 'profit' => $profit]);

                // Persist new stock for barang
                $barang->stok_barang = $currentStock;
                $barang->save();
                
                // Store for response
                $itemsData[] = [
                    'nama' => $barang->merk . ' ' . $barang->jenis,
                    'ukuran' => $barang->ukuran_kemasan,
                    'qty' => $item['qty'],
                    'harga' => $barang->harga_barang,
                    'subtotal' => $subtotal,
                ];
            }
            
            // Update transaksi totals
            $kembalian = max(0, ($data['bayar_tunai'] ?? 0) - $totalHarga);
            $transaksi->update([
                'total_harga' => $totalHarga,
                'kembalian' => $kembalian,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan',
            'data' => [
                'kode_transaksi' => $transaksi->kode_transaksi,
                'transaksi_id' => $transaksi->id,
                'jumlah_item' => count($itemsData),
                'total_harga' => $totalHarga,
                'bayar_tunai' => $data['bayar_tunai'] ?? 0,
                'kembalian' => $transaksi->kembalian,
                'items' => $itemsData,
            ],
        ]);
    }

    public function nota($id)
    {
        $transaksi = Transaksi::with(['items.barang'])->findOrFail($id);
        return view('pos.nota', compact('transaksi'));
    }
}
