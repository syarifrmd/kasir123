@extends('layouts.kasir')
@section('title','Data Barang')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Data Barang</h1>
    <a href="{{ route('barang.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Tambah Barang</a>
</div>

@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 mb-4 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 mb-4 rounded shadow-sm">
        {{ session('error') }}
    </div>
@endif

<!-- Search & Filter -->
<div class="bg-white p-4 rounded shadow mb-4">
    <form method="GET" action="{{ route('barang.index') }}" class="flex flex-col lg:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama / Kode / Kategori..." class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="w-full lg:w-48">
            <select name="sort" class="w-full border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Urut Nama A-Z</option>
                <option value="stok" {{ request('sort') == 'stok' ? 'selected' : '' }}>Stok Terbanyak</option>
                <option value="harga" {{ request('sort') == 'harga' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded text-sm hover:bg-gray-700">Cari</button>
        <a href="{{ route('barang.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300 flex items-center justify-center">Reset</a>
    </form>
</div>

<!-- Table -->
<div class="bg-white shadow rounded overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase font-medium">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Barang</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3 text-right">Harga Jual</th>
                    <th class="px-4 py-3 text-center">Stok</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($barang as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-gray-500">{{ $item->kode_barang }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $item->nama_barang }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ $item->kategori }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }} / {{ $item->satuan }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($item->stok <= 0)
                                <span class="text-red-600 font-bold">Habis</span>
                            @else
                                <span class="{{ $item->stok < 10 ? 'text-yellow-600 font-bold' : 'text-green-600 font-bold' }}">
                                    {{ $item->stok + 0 }} {{ $item->satuan }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('barang.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                <form action="{{ route('barang.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($barang->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $barang->links() }}
        </div>
    @endif
</div>
@endsection
