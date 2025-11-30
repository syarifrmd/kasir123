@extends('layouts.kasir')
@section('title','Data Transaksi')
@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Data Transaksi</h1>
        <a href="{{ route('transaksi.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Tambah</a>
    </div>

    <!-- Filter transaksi berdasarkan tanggal -->

    <div class="mb-4">
        <form method="GET" action="{{ route('transaksi.index') }}" class="flex items-center gap-2 text-sm">
            <label for="start" class="font-medium">Filter Tanggal:</label>
            <input type="date" id="start" name="start" value="{{ request('start') }}" class="border rounded px-2 py-1">
            <span>-</span>
            <input type="date" id="end" name="end" value="{{ request('end') }}" class="border rounded px-2 py-1">
            <button class="px-3 py-1.5 rounded bg-blue-600 text-white">Terapkan</button>
            @if(request('start') || request('end'))
                <a href="{{ route('transaksi.index') }}" class="px-3 py-1.5 rounded bg-gray-200 text-gray-800">Reset</a>
            @endif
        </form>

    </div>
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Kode Transaksi</th>
                    <th class="px-3 py-2 text-left">Tanggal</th>
                    <th class="px-3 py-2 text-left">Items</th>
                    <th class="px-3 py-2 text-left">Total Harga</th>
                    <th class="px-3 py-2 text-left">Metode</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transaksi as $t)
                <tr class="border-t">
                    <td class="px-3 py-2 font-mono">{{ $t->kode_transaksi }}</td>
                    <td class="px-3 py-2">{{ $t->tanggal_transaksi->format('d/m/Y') }}</td>
                    <td class="px-3 py-2">
                        @if($t->items->count() > 0)
                            <div class="text-xs space-y-1">
                                @foreach($t->items as $item)
                                    <div>{{ $item->barang->merk ?? '-' }} {{ $item->barang->jenis ?? '' }} ({{ $item->qty }}x)</div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 font-semibold">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 capitalize">{{ str_replace('_',' ',$t->metode_transaksi) }}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs rounded {{ $t->status_transaksi=='lunas' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-800' }}">{{ $t->status_transaksi }}</span>
                    </td>
                    <td class="px-3 py-2 text-right">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ url('/pos/nota/' . $t->id) }}" class="text-blue-600 hover:underline text-sm">Lihat Nota</a>
                            <form action="{{ route('transaksi.destroy',$t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-3 py-4 text-center text-gray-500">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $transaksi->links() }}</div>
@endsection
