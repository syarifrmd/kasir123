@extends('layouts.kasir')
@section('title','Data Transaksi')
@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Data Transaksi</h1>
        <a href="{{ route('transaksi.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Tambah</a>
    </div>

    <!-- Filter & Ringkasan -->
    <div class="mb-6 space-y-4">
        <!-- Filter Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center bg-white p-4 rounded shadow-sm">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('transaksi.index', ['period' => 'harian']) }}" 
                   class="px-4 py-2 rounded text-sm font-medium transition-colors {{ request('period') == 'harian' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   Harian
                </a>
                <a href="{{ route('transaksi.index', ['period' => 'mingguan']) }}" 
                   class="px-4 py-2 rounded text-sm font-medium transition-colors {{ request('period') == 'mingguan' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   Mingguan
                </a>
                <a href="{{ route('transaksi.index', ['period' => 'bulanan']) }}" 
                   class="px-4 py-2 rounded text-sm font-medium transition-colors {{ request('period') == 'bulanan' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   Bulanan
                </a>
                <a href="{{ route('transaksi.index', ['period' => 'tahunan']) }}" 
                   class="px-4 py-2 rounded text-sm font-medium transition-colors {{ request('period') == 'tahunan' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                   Tahunan
                </a>
                @if(request('period') || request('start'))
                    <a href="{{ route('transaksi.index') }}" class="px-4 py-2 rounded text-sm font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
            
            <form method="GET" action="{{ route('transaksi.index') }}" class="flex items-center gap-2 text-sm w-full sm:w-auto">
                <input type="date" name="start" value="{{ request('start') }}" class="border rounded px-2 py-2 w-full sm:w-auto" placeholder="Dari">
                <span class="text-gray-400">-</span>
                <input type="date" name="end" value="{{ request('end') }}" class="border rounded px-2 py-2 w-full sm:w-auto" placeholder="Sampai">
                <button class="px-4 py-2 rounded bg-gray-800 text-white hover:bg-gray-700 font-medium">Filter</button>
            </form>
        </div>

        <!-- Ringkasan Laporan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded shadow-sm border-l-4 border-blue-500">
                <div class="text-gray-500 text-sm font-medium">Total Omset (Periode Ini)</div>
                <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow-sm border-l-4 border-green-500">
                <div class="text-gray-500 text-sm font-medium">Total Transaksi</div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($totalTransaksi ?? 0, 0, ',', '.') }} <span class="text-sm font-normal text-gray-500">transaksi</span></div>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Kode Transaksi</th>
                    <th class="px-3 py-2 text-left">Tanggal</th>
                    <th class="px-3 py-2 text-left">Items</th>
                    <th class="px-3 py-2 text-left">Total Harga</th>
                    <th class="px-3 py-2 text-left">Pembeli</th>
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
                        @php($items = ($itemsByTrx[$t->kode_transaksi] ?? collect()))
                        @if($items->count() > 0)
                            <div class="text-xs space-y-1">
                                @foreach($items as $item)
                                    <div>
                                        @if(!empty($item->nama_display))
                                            {{ $item->nama_display }}
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                        ({{ $item->qty }}x)
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 font-semibold">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-xs">
                        @if($t->nama_customer)
                            <div><span class="font-medium">{{ $t->nama_customer }}</span></div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
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
