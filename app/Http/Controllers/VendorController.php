<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($w) use ($q){
                $w->where('nama','like',"%$q%")
                  ->orWhere('kode','like',"%$q%")
                  ->orWhere('nama_sales','like',"%$q%")
                  ->orWhere('no_kontak','like',"%$q%")
                  ->orWhere('telepon','like',"%$q%")
                  ->orWhere('alamat','like',"%$q%")
                  ->orWhere('kontak','like',"%$q%");
            });
        }
        $vendors = $query->orderBy('nama')->paginate(30)->withQueryString();
        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'nullable|string|max:20|unique:vendors,kode',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'no_kontak' => 'nullable|string|max:30',
            'nama_sales' => 'nullable|string|max:100',
        ]);
        Vendor::create($data);
        return redirect()->route('vendors.index')->with('success','Vendor berhasil ditambahkan');
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'kode' => 'nullable|string|max:20|unique:vendors,kode,' . $vendor->id,
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'no_kontak' => 'nullable|string|max:30',
            'nama_sales' => 'nullable|string|max:100',
        ]);
        $vendor->update($data);
        return redirect()->route('vendors.index')->with('success','Vendor berhasil diperbarui');
    }

    public function destroy(Vendor $vendor)
    {
        // Optional: check relations (stock batches)
        $hasBatches = $vendor->batches()->exists();
        if ($hasBatches) {
            return redirect()->route('vendors.index')->with('error','Tidak dapat menghapus vendor karena sudah digunakan pada batch stok.');
        }
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success','Vendor berhasil dihapus');
    }
}
