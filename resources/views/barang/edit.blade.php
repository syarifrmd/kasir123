@extends('layouts.kasir')
@section('title','Edit Barang')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 text-gray-500 text-sm">
            <a href="{{ route('barang.index') }}" class="hover:text-blue-600">Barang</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Edit Barang</span>
        </div>
        <a href="{{ route('barang.index') }}" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-orange-600 to-orange-700 text-white px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-bold">Edit: {{ $barang->nama_barang }}</h2>
            <p class="text-orange-100 text-sm mt-1">Perbarui informasi barang</p>
        </div>
        
        <form action="{{ route('barang.update', $barang->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="overflow-x-auto border-2 border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b-2">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 w-1/4">Field</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-3 font-semibold text-gray-700">Kode Barang</td>
                            <td class="px-4 py-3">
                                <div class="font-mono font-bold text-blue-700 bg-blue-50 px-3 py-2 rounded border inline-block">{{ $barang->kode_barang }}</div>
                                <p class="text-xs text-gray-400 mt-1">Kode tidak dapat diubah</p>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-3 font-semibold text-gray-700">Nama Barang <span class="text-red-500">*</span></td>
                            <td class="px-4 py-3">
                                <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none" required>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-3 font-semibold text-gray-700">Merk / Brand</td>
                            <td class="px-4 py-3">
                                <input type="text" name="merk" value="{{ old('merk', $barang->merk) }}" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-3 font-semibold text-gray-700">Varian / Ukuran</td>
                            <td class="px-4 py-3">
                                <input type="text" name="ukuran" value="{{ old('ukuran', $barang->ukuran) }}" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-3 font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></td>
                            <td class="px-4 py-3">
                                <select name="kategori" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none" required>
                                    <option value="RK" {{ $barang->kategori == 'RK' ? 'selected' : '' }}>RK - Rokok</option>
                                    <option value="SB" {{ $barang->kategori == 'SB' ? 'selected' : '' }}>SB - Sembako</option>
                                    <option value="MN" {{ $barang->kategori == 'MN' ? 'selected' : '' }}>MN - Minuman</option>
                                    <option value="SK" {{ $barang->kategori == 'SK' ? 'selected' : '' }}>SK - Snack</option>
                                    <option value="OB" {{ $barang->kategori == 'OB' ? 'selected' : '' }}>OB - Obat</option>
                                    <option value="EK" {{ $barang->kategori == 'EK' ? 'selected' : '' }}>EK - Elektronik</option>
                                    <option value="LL" {{ $barang->kategori == 'LL' ? 'selected' : '' }}>LL - Lain-lain</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-3 font-semibold text-gray-700">Satuan <span class="text-red-500">*</span></td>
                            <td class="px-4 py-3">
                                <input type="text" name="satuan" value="{{ old('satuan', $barang->satuan) }}" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none" required>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-3 font-semibold text-gray-700">Harga Jual (Rp) <span class="text-red-500">*</span></td>
                            <td class="px-4 py-3">
                                <input type="number" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none" required>
                            </td>
                        </tr>
                        <tr class="bg-yellow-50">
                            <td class="px-4 py-3 font-semibold text-gray-700">Stok Saat Ini</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-2xl text-gray-800">{{ $barang->stok + 0 }}</span>
                                    <span class="text-gray-600">{{ $barang->satuan }}</span>
                                    <span class="text-xs text-blue-600 bg-blue-100 px-3 py-1 rounded-full font-medium">Auto-managed</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">💡 Untuk mengubah stok, gunakan menu <strong>Manajemen Stok</strong></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('barang.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-bold flex items-center gap-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Update Barang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
