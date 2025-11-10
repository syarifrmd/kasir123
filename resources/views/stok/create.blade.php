@extends('layouts.kasir')
@section('title','Tambah Stok')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Tambah Stok Barang</h1>
    <a href="{{ route('stok.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Kembali</a>
</div>

<form action="{{ route('stok.store') }}" method="POST" x-data="stokForm()" class="max-w-3xl">
    @csrf
    <!-- Hidden Input untuk barang_id (disimpan di dalam form agar ikut submit) -->
    <input type="hidden" name="barang_id" x-model="selectedBarang" required>

    <!-- Single Card dengan semua field -->
    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        
        <!-- Header -->
        <div class="pb-6 border-b">
            <h2 class="font-bold text-lg text-gray-900">Form Tambah Stok Barang</h2>
            <p class="text-sm text-gray-600 mt-1">Lengkapi informasi stok yang masuk dengan vendor dan harga</p>
        </div>

        <!-- Pilih Barang dengan Search -->
        <div>
            <label class="block text-sm font-semibold mb-3 text-gray-700">Pilih Barang <span class="text-rose-600">*</span></label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kolom Kiri: List Barang -->
                <div class="border-2 border-gray-300 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 border-b font-medium text-sm text-gray-700">Daftar Barang</div>
                    <div class="max-h-64 overflow-y-auto">
                        <template x-for="barang in filteredBarang" :key="barang.id">
                            <div 
                                @click="selectBarang(barang)" 
                                class="px-4 py-2.5 cursor-pointer hover:bg-rose-50 border-b last:border-b-0 transition text-sm"
                                :class="{ 'bg-rose-100 border-l-4 border-l-rose-600 font-medium': selectedBarang === barang.id.toString() }"
                            >
                                <div class="font-medium text-gray-900" x-text="barang.merk + ' ' + barang.jenis"></div>
                                <div class="text-xs text-gray-600 mt-0.5">
                                    <span x-text="barang.kode_barang"></span> • <span x-text="barang.ukuran_kemasan"></span>
                                </div>
                            </div>
                        </template>
                        <div x-show="filteredBarang.length === 0" class="px-4 py-8 text-center text-gray-500 text-sm">
                            Barang tidak ditemukan
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Search Input -->
                <div>
                    <div class="relative mb-3">
                        <input 
                            type="text" 
                            x-model="searchBarang" 
                            @input="filterBarang()"
                            placeholder="Cari barang: merk, jenis, kode..."
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-200 transition"
                        >
                        <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">Ketik untuk mencari barang</p>

                    <!-- Selected Barang Info -->
                    <div x-show="selectedBarang" class="space-y-2">
                        <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-lg p-3 border border-rose-100">
                            <div class="text-xs text-gray-700 font-medium">Nama Barang</div>
                            <div class="text-sm font-bold text-rose-700 mt-1" x-text="selectedName"></div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg p-3 border border-blue-100">
                            <div class="text-xs text-gray-700 font-medium">Stok Saat Ini</div>
                            <div class="text-sm font-bold text-blue-700 mt-1" x-text="selectedStok + ' unit'"></div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-3 border border-green-100">
                            <div class="text-xs text-gray-700 font-medium">Harga Jual Saat Ini</div>
                            <div class="text-sm font-bold text-green-700 mt-1" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedHarga)"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Section -->
        <div class="pt-4 border-t">
            <label class="block text-sm font-semibold mb-3 text-gray-700">Vendor</label>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <select name="vendor_id" x-model="selectedVendor" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition">
                            <option value="">-- Pilih Vendor Existing --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->kode ?? 'NO-CODE' }} • {{ $v->nama }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih vendor jika sudah ada</p>
                    </div>
                    <div>
                        <input type="text" name="vendor_baru" x-model="vendorBaru" placeholder="Atau ketik nama vendor baru" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition">
                        <p class="text-xs text-gray-500 mt-1">Isi jika vendor belum ada di daftar</p>
                    </div>
                </div>
                <div x-show="vendorBaru" class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div>
                        <label class="block text-xs font-medium mb-1">Kode Vendor (opsional)</label>
                        <input type="text" name="vendor_baru_kode" placeholder="VND-XXXX (otomatis jika kosong)" class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Alamat</label>
                        <input type="text" name="vendor_baru_alamat" placeholder="Alamat vendor" class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">No Kontak</label>
                        <input type="text" name="vendor_baru_no_kontak" placeholder="0812xxxx" class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Nama Sales</label>
                        <input type="text" name="vendor_baru_nama_sales" placeholder="Nama sales" class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200">
                    </div>
                    <div class="col-span-1 sm:col-span-2 text-xs text-blue-700 mt-2">Informasi vendor baru akan otomatis disimpan saat stok disubmit.</div>
                </div>
            </div>
        </div>

        <!-- Qty & Harga Section -->
        <div class="pt-4 border-t">
            <label class="block text-sm font-semibold mb-3 text-gray-700">Qty & Harga</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Qty Masuk <span class="text-rose-600">*</span></label>
                    <input type="number" name="qty" min="1" x-model.number="qty" placeholder="0" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition font-semibold" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">HPP / Unit <span class="text-rose-600">*</span></label>
                    <input type="number" step="0.01" name="unit_cost" x-model.number="unitCost" placeholder="0" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition font-semibold" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Harga Jual Baru (opsional)</label>
                    <input type="number" step="0.01" name="harga_jual_baru" x-model.number="hargaJualBaru" placeholder="Kosong = tidak berubah" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200 transition">
                </div>
            </div>

            <!-- Total HPP Preview -->
            <div x-show="qty > 0 && unitCost > 0" class="mt-4 pt-4 border-t bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Total HPP untuk batch ini:</span>
                    <span class="text-xl font-bold text-green-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(qty * unitCost)"></span>
                </div>
                <p class="text-xs text-gray-600 mt-2"><span x-text="qty"></span> unit × Rp <span x-text="new Intl.NumberFormat('id-ID').format(unitCost)"></span></p>
            </div>
        </div>

        <!-- Catatan Section -->
        <div class="pt-4 border-t">
            <label class="block text-sm font-semibold mb-3 text-gray-700">Catatan (Opsional)</label>
            <input type="text" name="notes" x-model="notes" placeholder="Misal: PO#12345, Invoice#XYZ, Referensi pembelian, dll" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 transition">
            <p class="text-xs text-gray-500 mt-1">Informasi tambahan untuk referensi transaksi pembelian</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-6 border-t">
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-rose-600 text-white font-semibold text-sm hover:bg-rose-700 transition flex items-center gap-2 shadow-md hover:shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Simpan Stok
            </button>
            <a href="{{ route('stok.index') }}" class="px-6 py-2.5 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">Batal</a>
        </div>
    </div>
</form>

<script>
function stokForm() {
    return {
        searchBarang: '',
        selectedBarang: '',
        selectedName: '',
        selectedStok: '',
        selectedHarga: 0,
        selectedVendor: '',
        vendorBaru: '',
        qty: '',
        unitCost: '',
        hargaJualBaru: '',
        notes: '',
        filteredBarang: [],
        allBarang: [],

        filterBarang() {
            const query = this.searchBarang.toLowerCase().trim();
            
            if (!query) {
                this.filteredBarang = this.allBarang;
            } else {
                this.filteredBarang = this.allBarang.filter(b => 
                    b.merk.toLowerCase().includes(query) ||
                    b.jenis.toLowerCase().includes(query) ||
                    b.kode_barang.toLowerCase().includes(query) ||
                    b.ukuran_kemasan.toLowerCase().includes(query)
                );
            }
        },

        selectBarang(barang) {
            this.selectedBarang = barang.id.toString();
            this.selectedName = barang.merk + ' ' + barang.jenis;
            this.selectedStok = barang.stok_barang;
            this.selectedHarga = parseInt(barang.harga_barang) || 0;
            this.searchBarang = this.selectedName;
            document.querySelector('input[name="barang_id"]').value = barang.id;
        },

        init() {
            this.allBarang = window.stokBarangData || [];
            this.filteredBarang = this.allBarang;
        }
    }
}
</script>

<script>
// Store barang data globally for Alpine.js
window.stokBarangData = @json($barang);
</script>

@endsection
