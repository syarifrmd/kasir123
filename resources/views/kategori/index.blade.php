@extends('layouts.kasir')
@section('title','Kategori Barang')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Kategori Barang</h1>
    <a href="{{ route('barang.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">+ Tambah Barang</a>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-1 bg-white p-4 rounded shadow">
    <h3 class="font-semibold mb-3">Tambah Kategori</h3>
    <form method="POST" action="{{ route('kategori.store') }}" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Kode (2 huruf)</label>
        <input name="kode" maxlength="2" pattern="[A-Z]{2}" class="w-full border rounded px-3 py-2 text-sm uppercase" placeholder="MN" required>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Nama Kategori</label>
        <input name="nama" class="w-full border rounded px-3 py-2 text-sm" placeholder="Minuman" required>
      </div>
      <button class="px-4 py-2 bg-green-600 text-white rounded text-sm">Simpan</button>
    </form>
  </div>
  <div class="lg:col-span-2 bg-white p-4 rounded shadow">
    <h3 class="font-semibold mb-3">Daftar Kategori</h3>
    <table class="min-w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-3 py-2 text-left">Kode</th>
          <th class="px-3 py-2 text-left">Nama</th>
          <th class="px-3 py-2 text-left">Sumber</th>
          <th class="px-3 py-2 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($list as $item)
          <tr>
            <td class="px-3 py-2 font-mono">{{ $item['kode'] }}</td>
            <td class="px-3 py-2">
              @if($item['source'] === 'custom')
              <form method="POST" action="{{ route('kategori.update', $item['kode']) }}" class="flex gap-2 items-center">
                @csrf
                @method('PUT')
                <input name="nama" value="{{ $item['nama'] }}" class="border rounded px-2 py-1 text-sm flex-1">
                <button class="px-3 py-1 bg-indigo-600 text-white rounded text-xs">Update</button>
              </form>
              @else
                {{ $item['nama'] }}
              @endif
            </td>
            <td class="px-3 py-2">
                <span class="text-xs px-2 py-1 rounded {{ $item['source']==='custom' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $item['source']==='custom' ? 'Custom' : 'Default' }}
                </span>
            </td>
            <td class="px-3 py-2 text-right">
              @if($item['source'] === 'custom')
              <form method="POST" action="{{ route('kategori.destroy', $item['kode']) }}" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
                @csrf
                @method('DELETE')
                <button class="text-red-600 hover:underline text-xs">Hapus</button>
              </form>
              @else
                <span class="text-xs text-gray-400">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">Belum ada kategori</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
