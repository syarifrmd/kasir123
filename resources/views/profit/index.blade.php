@extends('layouts.kasir')
@section('title','Ringkasan Keuntungan')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Ringkasan Keuntungan</h1>
    <form method="GET" class="flex items-center gap-2 text-sm">
        <input type="date" name="start" value="{{ $start }}" class="border rounded px-2 py-1">
        <span>-</span>
        <input type="date" name="end" value="{{ $end }}" class="border rounded px-2 py-1">
        <button class="px-3 py-1.5 rounded bg-rose-600 text-white">Filter</button>
    </form>
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-right">Penjualan</th>
                <th class="px-3 py-2 text-right">HPP</th>
                <th class="px-3 py-2 text-right">Profit</th>
                <th class="px-3 py-2 text-right">Item</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @php($totalPenjualan = 0)
            @php($totalHpp = 0)
            @php($totalProfit = 0)
            @foreach($rows as $r)
                @php($totalPenjualan += $r->penjualan)
                @php($totalHpp += $r->hpp)
                @php($totalProfit += $r->profit)
                <tr>
                    <td class="px-3 py-2">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($r->penjualan,0,',','.') }}</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($r->hpp,0,',','.') }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-rose-700">Rp {{ number_format($r->profit,0,',','.') }}</td>
                    <td class="px-3 py-2 text-right">{{ $r->item_count }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-50 font-semibold">
                <td class="px-3 py-2 text-right">Total</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($totalPenjualan,0,',','.') }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($totalHpp,0,',','.') }}</td>
                <td class="px-3 py-2 text-right text-rose-700">Rp {{ number_format($totalProfit,0,',','.') }}</td>
                <td class="px-3 py-2 text-right"></td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
