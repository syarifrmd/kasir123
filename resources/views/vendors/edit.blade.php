@extends('layouts.kasir')
@section('title','Edit Kontak')
@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-lg font-bold mb-4">Edit Kontak</h2>
    <form action="{{ route('vendors.update', $vendor->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block text-sm mb-1">Nama</label>
            <input type="text" name="name" value="{{ $vendor->name }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="bg-gray-50 p-2 mb-4 rounded text-sm text-gray-500">
            Tipe: {{ $vendor->tipe }} (Tidak dapat diubah)
        </div>
        <div class="mb-4">
            <label class="block text-sm mb-1">No HP</label>
            <input type="text" name="hp" value="{{ $vendor->hp }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm mb-1">Alamat</label>
            <textarea name="alamat" class="w-full border rounded px-3 py-2">{{ $vendor->alamat }}</textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('vendors.index') }}" class="px-4 py-2 text-gray-600 bg-gray-100 rounded">Batal</a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
        </div>
    </form>
</div>
@endsection
