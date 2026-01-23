@extends('layouts.kasir')
@section('title','Riwayat Transaksi')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Riwayat Transaksi (Ledger)</h1>
    <a href="{{ route('transaksi.create') }}" class="px-4 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700 font-bold shadow">+ Kasir Baru</a>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-blue-50 border border-blue-200 p-4 rounded shadow-sm">
        <h3 class="text-sm text-blue-600 font-semibold mb-1">Total Pembelian (MASUK)</h3>
        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalMasuk ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="bg-green-50 border border-green-200 p-4 rounded shadow-sm">
        <h3 class="text-sm text-green-600 font-semibold mb-1">Total Penjualan (KELUAR)</h3>
        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalKeluar ?? 0, 0, ',', '.') }}</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white p-4 rounded shadow mb-4">
    <form method="GET" action="{{ route('transaksi.index') }}" class="flex flex-col lg:flex-row gap-3">
        <div class="flex-1 grid grid-cols-2 gap-2">
            <div>
                <label class="text-xs text-gray-500">Mulai</label>
                <input type="date" name="start" value="{{ request('start') }}" class="w-full border rounded px-2 py-1 text-sm">
            </div>
            <div>
                <label class="text-xs text-gray-500">Sampai</label>
                <input type="date" name="end" value="{{ request('end') }}" class="w-full border rounded px-2 py-1 text-sm">
            </div>
        </div>
        <div class="w-full lg:w-40">
            <label class="text-xs text-gray-500">Jenis</label>
            <select name="type" class="w-full border rounded px-2 py-1 text-sm" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="MASUK" {{ request('type') == 'MASUK' ? 'selected' : '' }}>MASUK (Beli)</option>
                <option value="KELUAR" {{ request('type') == 'KELUAR' ? 'selected' : '' }}>KELUAR (Jual)</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-4 py-1.5 bg-gray-700 text-white rounded text-sm h-full w-full">Filter</button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white shadow rounded overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase font-medium">
                <tr>
                    <th class="px-4 py-3">Tanggal / Waktu</th>
                    <th class="px-4 py-3">Kode TRX</th>
                    <th class="px-4 py-3 text-center">Jenis</th>
                    <th class="px-4 py-3">Barang</th>
                    <th class="px-4 py-3 text-right">Qty</th>
                    <th class="px-4 py-3 text-right">Harga Satuan</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-center">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transaksi as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">
                            {{ $trx->tanggal->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $trx->kode_transaksi }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($trx->jenis_transaksi == 'MASUK')
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-bold">IN</span>
                            @else
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold">OUT</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold block text-gray-800">{{ $trx->barang->nama_barang ?? '-' }}</span>
                            <span class="text-xs text-gray-500">{{ $trx->barang->kode_barang ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">
                            {{ $trx->qty + 0 }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">
                            {{ number_format($trx->harga_realisasi, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">
                            {{ number_format($trx->qty * $trx->harga_realisasi, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                            {{ $trx->user->name ?? 'System' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transaksi->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $transaksi->links() }}
        </div>
    @endif
</div>
@endsection
