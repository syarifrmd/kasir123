@extends('layouts.kasir')
@section('title','Data Barang')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Data Barang</h1>
    <div class="flex gap-2">
        <a href="{{ route('barang.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Tambah</a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<!-- Filter Section -->
<div class="bg-white p-4 rounded shadow mb-4">
    <form method="GET" action="{{ route('barang.index') }}" class="grid grid-cols-1 lg:grid-cols-6 gap-3">
        <div class="lg:col-span-2">
            <label class="block text-xs font-medium mb-1">Cari</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari merk / jenis / kode / ukuran" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Kategori</label>
            <select name="kategori" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Semua Kategori</option>
                @foreach(\App\Models\Barang::kategoriOptions() as $k => $label)
                    <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Vendor</label>
            <select name="vendor_id" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Semua Vendor</option>
                @foreach(($vendors ?? []) as $v)
                    <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Ketersediaan</label>
            <select name="available" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="ada" {{ request('available')==='ada' ? 'selected' : '' }}>Ada Stok</option>
                <option value="habis" {{ request('available')==='habis' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Urutkan</label>
            <select name="sort" class="w-full border rounded px-3 py-2 text-sm">
                <option value="nama" {{ request('sort')==='nama' ? 'selected' : '' }}>Nama (Merk/Jenis)</option>
                <option value="terbaru_masuk" {{ request('sort')==='terbaru_masuk' ? 'selected' : '' }}>Terbaru Masuk</option>
                <option value="terlama_masuk" {{ request('sort')==='terlama_masuk' ? 'selected' : '' }}>Terlama Masuk</option>
                <option value="stok" {{ request('sort')==='stok' ? 'selected' : '' }}>Stok Terbanyak</option>
                <option value="harga" {{ request('sort')==='harga' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Per Halaman</label>
            <select name="per_page" class="w-full border rounded px-3 py-2 text-sm">
                @foreach([20,50,100,200] as $n)
                    <option value="{{ $n }}" {{ (int)request('per_page',20)===$n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 lg:col-span-6">
            <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded text-sm">Terapkan</button>
            <a href="{{ route('barang.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm">Reset</a>
        </div>
    </form>
</div>

<div class="space-y-3">
@forelse($barangGrouped as $index => $group)
    <div class="bg-white shadow rounded overflow-hidden" x-data="{ open: false }">
        <!-- Header Group -->
        <div class="px-4 py-3 bg-gray-50 border-b flex items-center justify-between cursor-pointer hover:bg-gray-100" @click="open = !open">
            <div class="flex items-center gap-4 flex-1">
                <button type="button" class="text-gray-500 focus:outline-none">
                    <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700 font-medium">
                            {{ \App\Models\Barang::kategoriOptions()[$group['kategori']] ?? $group['kategori'] }}
                        </span>
                        <h3 class="font-semibold text-base">{{ $group['merk'] }}</h3>
                        <span class="text-xs text-gray-500">({{ $group['varians']->count() }} varian)</span>
                    </div>
                    @if($group['deskripsi'])
                        <p class="text-xs text-gray-600 mt-1">{{ $group['deskripsi'] }}</p>
                    @endif
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Total Stok</div>
                        <div class="text-lg font-semibold {{ $group['total_stok'] > 10 ? 'text-green-600' : ($group['total_stok'] > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $group['total_stok'] }}
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('barang.edit', $group['first_item']) }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700">
                            Edit Merk
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail Varians (Collapsible) -->
        <div x-show="open" x-collapse class="border-t">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Kode Barang</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Jenis</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Ukuran Kemasan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Harga</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Stok</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                @foreach($group['varians'] as $varian)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-xs text-gray-600">{{ $varian->kode_barang }}</td>
                        <td class="px-4 py-2">
                            <span class="font-medium">{{ $varian->jenis }}</span>
                            <span class="text-xs text-gray-400 ml-1">({{ substr($varian->kode_barang, 4, 2) }})</span>
                        </td>
                        <td class="px-4 py-2">{{ $varian->ukuran_kemasan }}</td>
                        <td class="px-4 py-2 text-rose-600 font-medium">Rp {{ number_format($varian->harga_barang, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs {{ $varian->stok_barang > 10 ? 'bg-green-100 text-green-700' : ($varian->stok_barang > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $varian->stok_barang }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            <form action="{{ route('barang.destroy', $varian) }}" method="POST" class="inline" onsubmit="return confirm('Hapus varian ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-xs">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="bg-white shadow rounded px-4 py-8 text-center text-gray-500">
        Belum ada data barang
    </div>
@endforelse
</div>

@isset($barangPaginated)
<div class="mt-4">{{ $barangPaginated->links() }}</div>
@endisset
@endsection
