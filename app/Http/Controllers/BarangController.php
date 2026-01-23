<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\TransaksiLedger;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        // Search
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($w) use ($q){
                $w->where('nama_barang', 'like', "%$q%")
                  ->orWhere('merk', 'like', "%$q%")
                  ->orWhere('kode_barang', 'like', "%$q%")
                  ->orWhere('kategori', 'like', "%$q%");
            });
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Availability filter
        if ($request->filled('available')) {
            if ($request->available === 'ada') $query->where('stok', '>', 0);
            if ($request->available === 'habis') $query->where('stok', '<=', 0);
        }

        // Sorting
        $sort = $request->input('sort', 'nama');
        if ($sort === 'stok') {
            $query->orderBy('stok', 'desc');
        } elseif ($sort === 'harga') {
            $query->orderBy('harga_jual', 'desc');
        } else {
            $query->orderBy('nama_barang');
        }

        $barang = $query->paginate(20)->withQueryString();

        return view('barang.index', [
            'barang' => $barang,
            'kategoris' => Barang::select('kategori')->distinct()->pluck('kategori'),
        ]);
    }

    public function create()
    {
        $vendors = \App\Models\Kontak::where('tipe', 'VENDOR')
            ->orderBy('name') // ✅ Diganti dari 'nama' ke 'name'
            ->get();

        return view('barang.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        // Validate base info and varians array
        $validated = $request->validate([
            'kategori' => 'required|string|max:10',
            'merk' => 'required|string|max:100',
            'satuan' => 'required|string|max:20',
            'vendor_id' => 'nullable|exists:kontak,id',
            'varians' => 'required|array|min:1',
            'varians.*.varian' => 'required|string|max:100',
            'varians.*.kemasan' => 'nullable|string|max:50',
            'varians.*.kode' => 'required|string|max:20|unique:barang,kode_barang',
            'varians.*.harga_beli' => 'nullable|numeric|min:0',
            'varians.*.harga' => 'required|numeric|min:0',
            'varians.*.stok' => 'nullable|numeric|min:0',
        ]);

        $userId = auth()->id() ?? 1;
        $vendorId = $validated['vendor_id'] ?? null;
        $created = [];

        // Loop through each variant and create as separate barang
        foreach ($validated['varians'] as $v) {
            // Build nama_barang from merk + varian
            $namaBarang = $validated['merk'];
            if (!empty($v['varian'])) {
                $namaBarang .= ' ' . $v['varian'];
            }

            $barang = Barang::create([
                'kode_barang' => $v['kode'],
                'nama_barang' => $namaBarang,
                'merk' => $validated['merk'],
                'ukuran' => $v['kemasan'] ?? null,
                'kategori' => $validated['kategori'],
                'satuan' => $validated['satuan'],
                'harga_jual' => $v['harga'],
                'stok' => 0,
            ]);

            // If initial stock provided, create MASUK transaction
            if (!empty($v['stok']) && $v['stok'] > 0) {
                $hargaRealisasi = !empty($v['harga_beli']) ? $v['harga_beli'] : 0;
                
                TransaksiLedger::create([
                    'kode_transaksi' => 'KULAKAN-' . strtoupper(uniqid()),
                    'tanggal' => now(),
                    'jenis_transaksi' => 'MASUK',
                    'barang_id' => $barang->id,
                    'kontak_id' => $vendorId,
                    'user_id' => $userId,
                    'qty' => $v['stok'],
                    'harga_realisasi' => $hargaRealisasi,
                    'status' => 'LUNAS',
                    'keterangan' => $vendorId ? 'Stok awal dari kulakan' : 'Stok awal',
                ]);
            }

            $created[] = $barang->nama_barang;
        }

        $message = count($created) . ' varian barang berhasil ditambahkan: ' . implode(', ', $created);
        return redirect()->route('barang.index')->with('success', $message);
    }

    public function edit(Barang $barang)
    {
        $vendors = \App\Models\Kontak::where('tipe', 'VENDOR')
            ->orderBy('name') // ✅ Diganti dari 'nama' ke 'name'
            ->get();

        return view('barang.edit', compact('barang', 'vendors'));
    }

    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'merk' => 'nullable|string|max:100',
            'ukuran' => 'nullable|string|max:50',
            'kategori' => 'required|string|max:50',
            'satuan' => 'required|string|max:20',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $barang->update($validated);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->history()->exists()) {
             return back()->with('error', 'Barang tidak bisa dihapus karena sudah ada transaksi.');
        }
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
