@extends('layouts.kasir')
@section('title','Detail Stok: ' . ($barang->merk . ' ' . $barang->jenis))
@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold">{{ $barang->merk }} {{ $barang->jenis }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ $barang->kode_barang }} • {{ $barang->ukuran_kemasan }} • {{ \App\Models\Barang::KATEGORI[$barang->kategori] ?? $barang->kategori }}</p>
        </div>
        <a href="{{ route('stok.create') }}" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Stok Lagi
        </a>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-600">
            <div class="text-xs text-gray-600 font-medium">Stok Tersisa</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $barang->stok_barang }}</div>
            <p class="text-xs text-gray-500 mt-1">unit</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-600">
            <div class="text-xs text-gray-600 font-medium">Harga Jual</div>
            <div class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</div>
            <p class="text-xs text-gray-500 mt-1">per unit</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-600">
            <div class="text-xs text-gray-600 font-medium">Total Batch</div>
            <div class="text-2xl font-bold text-purple-600 mt-1">{{ $batches->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">batch masuk</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Batch Masuk -->
    <div class="lg:col-span-1 bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-rose-50 to-orange-50">
            <h2 class="font-bold text-base">Riwayat Batch Masuk</h2>
            <p class="text-xs text-gray-600 mt-1">{{ $batches->count() }} batch tercatat</p>
        </div>
        <div class="divide-y max-h-96 overflow-y-auto">
            @forelse($batches as $bt)
                <div class="px-4 py-3 hover:bg-gray-50 text-sm">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $bt->vendor->nama ?? 'Vendor: -' }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $bt->received_at?->format('d M Y H:i') ?? $bt->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $bt->qty_remaining > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $bt->qty_remaining }} / {{ $bt->qty_received }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 space-y-0.5">
                        <div><span class="font-medium">HPP:</span> Rp {{ number_format($bt->unit_cost, 0, ',', '.') }}</div>
                        <div><span class="font-medium">Harga Jual:</span> Rp {{ number_format($bt->sell_price_at_receive ?? 0, 0, ',', '.') }}</div>
                    </div>
                    @if($bt->notes)
                        <div class="text-xs text-gray-500 mt-2 italic border-t pt-2">{{ $bt->notes }}</div>
                    @endif
                </div>
            @empty
                <div class="px-4 py-6 text-center text-gray-500 text-sm">Belum ada batch</div>
            @endforelse
        </div>
    </div>

    <!-- Riwayat Pergerakan Stok -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
            <h2 class="font-bold text-base">Riwayat Pergerakan Stok</h2>
            <p class="text-xs text-gray-600 mt-1">Catatan semua transaksi masuk/keluar/penyesuaian</p>
        </div>
        <div class="flex-1 overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Waktu</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Tipe</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Vendor</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Qty</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Stok</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">HPP</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Harga Jual</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($movements as $mv)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $mv->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold inline-flex items-center gap-1
                                    {{ $mv->type === 'in' ? 'bg-green-100 text-green-700' : ($mv->type === 'out' ? 'bg-rose-100 text-rose-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        @if($mv->type === 'in')
                                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd"></path>
                                        @elseif($mv->type === 'out')
                                            <path fill-rule="evenodd" d="M8 13a1 1 0 110 2H3a1 1 0 01-1-1V9a1 1 0 112 0v3.586l4.293-4.293a1 1 0 011.414 1.414L6.414 13H8z" clip-rule="evenodd"></path>
                                        @else
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a1 1 0 001 1h12a1 1 0 001-1V6a2 2 0 00-2-2H4zm12 12H4a2 2 0 01-2-2v-4a1 1 0 00-1-1H.5a1 1 0 000 2H1v4a2 2 0 002 2h12a2 2 0 002-2v-4a1 1 0 001-1h.5a1 1 0 000-2h-.5a1 1 0 00-1 1v4a2 2 0 01-2 2z" clip-rule="evenodd"></path>
                                        @endif
                                    </svg>
                                    {{ strtoupper($mv->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $mv->vendor->nama ?? '-' }}</td>
                            <td class="px-4 py-2 text-right font-medium">{{ $mv->qty }}</td>
                            <td class="px-4 py-2 text-right">
                                <span class="text-gray-600">{{ $mv->before_stock ?? '-' }} → {{ $mv->after_stock ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-2 text-right">{{ $mv->unit_cost ? 'Rp ' . number_format($mv->unit_cost, 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-2 text-right">{{ $mv->unit_price ? 'Rp ' . number_format($mv->unit_price, 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-2 text-xs">{{ $mv->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat pergerakan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Back Button -->
<div class="flex gap-2 mt-6">
    <a href="{{ route('stok.index') }}" class="px-4 py-2 rounded-lg border text-sm font-medium hover:bg-gray-50 transition">← Kembali ke Manajemen Stok</a>
</div>

@endsection
