@extends('layouts.kasir')
@section('title','Dashboard')
@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Card 1 -->
    <div class="bg-white p-4 rounded shadow border-l-4 border-blue-500">
        <div class="text-gray-500 text-sm">Total Produk</div>
        <div class="text-2xl font-bold">{{ $totalProduk }}</div>
    </div>
    <!-- Card 2 -->
    <div class="bg-white p-4 rounded shadow border-l-4 border-green-500">
        <div class="text-gray-500 text-sm">Omset Hari Ini</div>
        <div class="text-2xl font-bold">Rp {{ number_format($omsetToday, 0, ',', '.') }}</div>
    </div>
    <!-- Card 3 -->
    <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
        <div class="text-gray-500 text-sm">Stok Menipis</div>
        <div class="text-2xl font-bold">{{ $lowStock }}</div>
    </div>
    <!-- Card 4 -->
    <div class="bg-white p-4 rounded shadow border-l-4 border-red-500">
        <div class="text-gray-500 text-sm">Stok Habis (0)</div>
        <div class="text-2xl font-bold">{{ $outOfStock }}</div>
    </div>
</div>

<div class="bg-white p-6 rounded shadow">
    <h2 class="text-lg font-bold mb-4">Transaksi Terakhir</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">Waktu</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Barang</th>
                    <th class="px-4 py-2 text-right">Qty</th>
                    <th class="px-4 py-2 text-right">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTrx as $trx)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $trx->tanggal->format('d M H:i') }}</td>
                    <td class="px-4 py-2">
                        @if($trx->jenis_transaksi == 'MASUK')
                            <span class="text-blue-600 font-bold">IN</span>
                        @else
                            <span class="text-green-600 font-bold">OUT</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $trx->barang->nama_barang ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ $trx->qty + 0 }}</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($trx->qty * $trx->harga_realisasi, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-right">
        <a href="{{ route('transaksi.index') }}" class="text-blue-600 hover:underline">Lihat Semua Transaksi &rarr;</a>
    </div>
</div>
@endsection
