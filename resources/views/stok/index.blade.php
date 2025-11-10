@extends('layouts.kasir')
@section('title','Manajemen Stok')
@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Manajemen Stok Barang</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('stok.create') }}" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Stok
            </a>
            <a href="{{ route('profit.index') }}" class="px-4 py-2 rounded-lg border text-sm font-medium hover:bg-gray-50 transition">Ringkasan Keuntungan</a>
        </div>
    </div>

    <!-- Filter Section -->
    <form method="GET" x-data="{showFilters: false}" class="space-y-3">
        <div class="flex items-center gap-2 mb-3">
            <button type="button" @click="showFilters = !showFilters" class="text-sm px-3 py-2 rounded border hover:bg-gray-50 flex items-center gap-2 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                {{ request()->filled('q') || request()->filled('kategori') || request()->filled('available') || request()->filled('vendor_id') ? 'Filter Aktif ✓' : 'Tambah Filter' }}
            </button>
            @if(request()->filled('q') || request()->filled('kategori') || request()->filled('available') || request()->filled('vendor_id') || request()->filled('tanggal_mulai') || request()->filled('tanggal_akhir') || request()->filled('sort') || request()->filled('per_page'))
                <a href="{{ route('stok.index') }}" class="text-sm px-3 py-2 rounded border text-rose-600 hover:bg-rose-50 font-medium">Reset Filter</a>
            @endif
        </div>

        <div x-show="showFilters" class="bg-white rounded-lg shadow p-4 space-y-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Cari Barang</label>
                <input type="text" name="q" value="{{ request('q') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Merk, jenis, kode...">
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Kategori</label>
                <select name="kategori" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Semua Kategori --</option>
                    @foreach(\App\Models\Barang::KATEGORI as $k => $label)
                        <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $label }} ({{ $k }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Availability -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Ketersediaan</label>
                <select name="available" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Semua Status --</option>
                    <option value="ada" {{ request('available') == 'ada' ? 'selected' : '' }}>Ada Stok</option>
                    <option value="habis" {{ request('available') == 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>

            <!-- Vendor -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Vendor</label>
                <select name="vendor_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Semua Vendor --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sorting -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Sortir</label>
                <select name="sort" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama Barang</option>
                    <option value="terbaru_masuk" {{ request('sort') == 'terbaru_masuk' ? 'selected' : '' }}>Terbaru Masuk</option>
                    <option value="terlama_masuk" {{ request('sort') == 'terlama_masuk' ? 'selected' : '' }}>Terlama Masuk</option>
                    <option value="stok_banyak" {{ request('sort') == 'stok_banyak' ? 'selected' : '' }}>Stok Terbanyak</option>
                    <option value="stok_sedikit" {{ request('sort') == 'stok_sedikit' ? 'selected' : '' }}>Stok Tersedikit</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Per Halaman</label>
                <select name="per_page" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 item</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 item</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 item</option>
                </select>
            </div>

            <!-- Button Apply -->
            <div class="sm:col-span-2 lg:col-span-5 flex gap-2 pt-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Terapkan Filter</button>
            </div>
        </div>
    </form>
</div>

<!-- Hasil Summary -->
<div class="mb-4 text-sm text-gray-600">
    Menampilkan <span class="font-semibold">{{ $barang->count() }}</span> dari <span class="font-semibold">{{ $totalCount ?? $barang->count() }}</span> barang
    @if(request()->filled('q'))
        | Pencarian: <span class="font-mono">{{ request('q') }}</span>
    @endif
</div>

<!-- Tabel Barang -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b sticky top-0">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Barang</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Kategori</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-700">Stok Tersisa</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga Jual</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-700">Batch</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($barang as $b)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $b->merk }} {{ $b->jenis }}</div>
                        <div class="text-xs text-gray-500">{{ $b->kode_barang }} • {{ $b->ukuran_kemasan }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                            {{ \App\Models\Barang::KATEGORI[$b->kategori] ?? $b->kategori }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="font-semibold">{{ $b->qty_tersisa ?? 0 }}</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="font-medium">Rp {{ number_format($b->harga_barang, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold">
                            {{ $b->total_batches }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if(($b->qty_tersisa ?? 0) > 0)
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Ada
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                Habis
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('stok.show', $b->id) }}" class="text-rose-600 hover:text-rose-700 font-medium text-sm">Lihat Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        Tidak ada barang yang sesuai dengan filter
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination (if needed) -->
@if(method_exists($barang, 'links'))
    <div class="mt-4">
        {{ $barang->links() }}
    </div>
@endif

@endsection
