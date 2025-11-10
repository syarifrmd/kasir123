@extends('layouts.kasir')
@section('title','Tambah Transaksi')
@section('content')
    <h1 class="text-xl font-semibold mb-4">Tambah Transaksi</h1>
    <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow max-w-xl">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Barang</label>
            <select name="kode_barang" class="w-full border rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($barang as $b)
                    <option value="{{ $b->kode_barang }}" @selected(old('kode_barang')==$b->kode_barang)>{{ $b->merk }} {{ $b->jenis }} (Stok: {{ $b->stok_barang }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Jumlah</label>
                <input type="number" name="volume_barang" value="{{ old('volume_barang') }}" class="w-full border rounded px-3 py-2 text-sm" min="1" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal</label>
                <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', now()->toDateString()) }}" class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Metode Pembayaran</label>
                <select name="metode_transaksi" class="w-full border rounded px-3 py-2 text-sm" required>
                    <option value="">-- Pilih --</option>
                    <option value="qris" @selected(old('metode_transaksi')=='qris')>Qris</option>
                    <option value="transfer_bank" @selected(old('metode_transaksi')=='transfer_bank')>Transfer Bank</option>
                    <option value="cash" @selected(old('metode_transaksi')=='cash')>Cash</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Status Pembayaran</label>
                <select name="status_transaksi" class="w-full border rounded px-3 py-2 text-sm" required>
                    <option value="lunas" @selected(old('status_transaksi')=='lunas')>Lunas</option>
                    <option value="belum_lunas" @selected(old('status_transaksi')=='belum_lunas')>Belum Lunas</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan</button>
            <a href="{{ route('transaksi.index') }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</a>
        </div>
    </form>
@endsection
