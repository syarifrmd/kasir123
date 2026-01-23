<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kontak;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Kontak::query()->vendor(); // Scope defined in Model

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $vendors = $query->paginate(20);

        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        
        Kontak::create([
            'name' => $request->name,
            'tipe' => 'VENDOR',
            'hp'   => $request->hp,
            'alamat'=> $request->alamat
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil ditambahkan');
    }

    public function edit(Kontak $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Kontak $vendor)
    {
        $request->validate(['name' => 'required']);
        $vendor->update($request->only('name','hp','alamat'));
        return redirect()->route('vendors.index')->with('success', 'Vendor updated');
    }
    
    public function destroy(Kontak $vendor)
    {
        $vendor->delete();
        return back()->with('success', 'Vendor deleted');
    }
}
