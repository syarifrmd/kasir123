@extends('layouts.kasir')
@section('title','Data Vendor')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Data Vendor</h1>
    <a href="{{ route('vendors.create') }}" class="px-3 py-2 bg-orange-600 text-white rounded text-sm">+ Tambah Vendor</a>
  </div>

<form method="GET" class="mb-4">
  <div class="flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari: nama, kode, sales, kontak" class="w-72 border rounded px-3 py-2 text-sm">
    <button class="px-3 py-2 bg-gray-800 text-white rounded text-sm">Cari</button>
  </div>
</form>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-100 text-gray-600 text-xs">
      <tr>
        <th class="px-4 py-2 text-left">Kode</th>
        <th class="px-4 py-2 text-left">Nama Vendor</th>
        <th class="px-4 py-2 text-left">Alamat</th>
        <th class="px-4 py-2 text-left">Telepon</th>
        <th class="px-4 py-2 text-left">No Kontak</th>
        <th class="px-4 py-2 text-left">Nama Sales</th>
        <th class="px-4 py-2 text-left">Kontak</th>
        <th class="px-4 py-2 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @forelse($vendors as $v)
      <tr class="hover:bg-gray-50">
        <td class="px-4 py-2 font-mono text-xs">{{ $v->kode ?? '-' }}</td>
        <td class="px-4 py-2 font-medium">{{ $v->nama }}</td>
        <td class="px-4 py-2">{{ $v->alamat }}</td>
        <td class="px-4 py-2">{{ $v->telepon }}</td>
        <td class="px-4 py-2">{{ $v->no_kontak }}</td>
        <td class="px-4 py-2">{{ $v->nama_sales }}</td>
        <td class="px-4 py-2">{{ $v->kontak }}</td>
        <td class="px-4 py-2 text-right whitespace-nowrap">
          <a href="{{ route('vendors.edit',$v) }}" class="text-indigo-600 hover:underline text-xs">Edit</a>
          <form action="{{ route('vendors.destroy',$v) }}" method="POST" class="inline" onsubmit="return confirm('Hapus vendor ini?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline text-xs ml-2">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500">Belum ada vendor</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-3">{{ $vendors->links() }}</div>
@endsection
