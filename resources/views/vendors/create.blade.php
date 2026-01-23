@extends('layouts.kasir')
@section('title','Tambah Kontak')
@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-lg font-bold mb-4">Tambah Kontak Baru</h2>
    <form action="{{ route('vendors.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm mb-1">Nama</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm mb-1">Tipe</label>
            <select name="tipe" class="w-full border rounded px-3 py-2">
                <option value="VENDOR">Vendor (Supplier)</option>
                <option value="CUSTOMER">Customer (Pelanggan)</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm mb-1">No HP</label>
            <input type="text" name="hp" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm mb-1">Alamat</label>
            <textarea name="alamat" class="w-full border rounded px-3 py-2"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('vendors.index') }}" class="px-4 py-2 text-gray-600 bg-gray-100 rounded">Batal</a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
