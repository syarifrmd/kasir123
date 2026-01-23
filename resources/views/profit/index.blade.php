@extends('layouts.kasir')
@section('title','Profit & Loss')
@section('content')
<h1 class="text-2xl font-bold mb-6">Laporan Profit & Loss (Cash Flow)</h1>

<!-- Filter -->
<div class="bg-white p-4 rounded shadow mb-6">
    <form method="GET" class="flex gap-4 items-end">
        <div>
            <label class="block text-sm text-gray-500 mb-1">Dari</label>
            <input type="date" name="start" value="{{ $start }}" class="border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-500 mb-1">Sampai</label>
            <input type="date" name="end" value="{{ $end }}" class="border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tampilkan</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow border-t-4 border-green-500">
        <h3 class="text-gray-500 font-medium mb-2">Penjualan (Cash IN)</h3>
        <p class="text-3xl font-bold text-green-600">+ Rp {{ number_format($omset, 0, ',', '.') }}</p>
        <p class="text-sm text-gray-400 mt-2">Total 'KELUAR' amount</p>
    </div>

    <div class="bg-white p-6 rounded shadow border-t-4 border-red-500">
        <h3 class="text-gray-500 font-medium mb-2">Pembelian (Cash OUT)</h3>
        <p class="text-3xl font-bold text-red-600">- Rp {{ number_format($pembelian, 0, ',', '.') }}</p>
        <p class="text-sm text-gray-400 mt-2">Total 'MASUK' amount</p>
    </div>

    <div class="bg-gray-800 p-6 rounded shadow border-t-4 {{ $grossProfit >= 0 ? 'border-blue-400' : 'border-orange-500' }}">
        <h3 class="text-gray-400 font-medium mb-2">Cash Flow Bersih</h3>
        <p class="text-3xl font-bold {{ $grossProfit >= 0 ? 'text-blue-400' : 'text-orange-400' }}">
            Rp {{ number_format($grossProfit, 0, ',', '.') }}
        </p>
        <p class="text-sm text-gray-500 mt-2">Selisih Masuk & Keluar</p>
    </div>
</div>
@endsection
