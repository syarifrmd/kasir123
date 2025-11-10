@extends('layouts.kasir')
@section('title','Tambah Vendor')
@section('content')
<h1 class="text-xl font-semibold mb-4">Tambah Vendor</h1>
<form action="{{ route('vendors.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow max-w-3xl">
  @csrf
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Kode (Opsional)</label>
      <input type="text" name="kode" value="{{ old('kode') }}" placeholder="VND-XXXX" class="w-full border rounded px-3 py-2 text-sm">
      <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk auto-generate</p>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Nama Vendor <span class="text-red-600">*</span></label>
      <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
  </div>
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Alamat</label>
      <input type="text" name="alamat" value="{{ old('alamat') }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Telepon</label>
      <input type="text" name="telepon" value="{{ old('telepon') }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
  </div>
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">No Kontak</label>
      <input type="text" name="no_kontak" value="{{ old('no_kontak') }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Nama Sales</label>
      <input type="text" name="nama_sales" value="{{ old('nama_sales') }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Kontak Tambahan / Catatan</label>
    <input type="text" name="kontak" value="{{ old('kontak') }}" class="w-full border rounded px-3 py-2 text-sm" placeholder="Email, WA, dll">
  </div>
  <div class="flex gap-2 pt-2">
    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded text-sm">Simpan</button>
    <a href="{{ route('vendors.index') }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</a>
  </div>
</form>
@endsection
