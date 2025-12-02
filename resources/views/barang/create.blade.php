@extends('layouts.kasir')
@section('title','Tambah Barang')
@section('content')
<h1 class="text-xl font-semibold mb-4">Tambah Barang</h1>
<form action="{{ route('barang.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow max-w-3xl" x-data="barangForm()">
    @csrf
    <!-- Pilih Kategori -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <h3 class="font-semibold text-blue-900 mb-3">Pilih Kategori</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kategori
                    <a href="{{ route('kategori.index') }}" target="_blank" class="ml-2 text-xs text-blue-600 hover:underline">+ Tambah Kategori</a>
                </label>
                <select name="kategori" class="w-full border rounded px-3 py-2 text-sm" required>
                    @foreach(\App\Models\Barang::kategoriOptions() as $k => $label)
                        <option value="{{ $k }}" {{ old('kategori') == $k ? 'selected' : '' }}>{{ $label }} ({{ $k }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nama Merk Barang</label>
                <input type="text" name="merk" value="{{ old('merk') }}" class="w-full border rounded px-3 py-2 text-sm" required placeholder="Contoh: Aqua, Indomie">
            </div>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Deskripsi</label>
        <textarea name="deskripsi" rows="2" class="w-full border rounded px-3 py-2 text-sm" placeholder="Deskripsi umum merk barang">{{ old('deskripsi') }}</textarea>
    </div>
    
    <hr class="my-4">
    <div class="flex justify-between items-center mb-2">
        <h3 class="font-medium">Varian Jenis & Kemasan</h3>
        <button type="button" @click="addVarian()" class="text-sm px-3 py-1 bg-green-600 text-white rounded">+ Tambah Varian</button>
    </div>
    
    <div class="space-y-3">
        <template x-for="(varian, index) in varians" :key="index">
            <div class="border p-3 rounded bg-gray-50">
                <div class="grid grid-cols-12 gap-3 items-end mb-2">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Nama Jenis</label>
                        <input type="text" :name="'varians['+index+'][jenis]'" x-model="varian.jenis" class="w-full border rounded px-2 py-1 text-sm" required placeholder="Botol">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Kode Jenis</label>
                        <input type="text" :name="'varians['+index+'][kode_jenis]'" x-model="varian.kode_jenis" class="w-full border rounded px-2 py-1 text-sm" maxlength="2" placeholder="01 (auto)">
                        <p class="text-xs text-gray-400">Kosongkan = 01</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Kode Kemasan</label>
                        <input type="text" :name="'varians['+index+'][kode_kemasan]'" x-model="varian.kode_kemasan" class="w-full border rounded px-2 py-1 text-sm" maxlength="2" placeholder="01">
                        <p class="text-xs text-gray-400">2 digit (01-99)</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Ukuran/Kemasan</label>
                        <input type="text" :name="'varians['+index+'][ukuran_kemasan]'" x-model="varian.ukuran_kemasan" class="w-full border rounded px-2 py-1 text-sm" required placeholder="600ml">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Harga</label>
                        <input type="number" :name="'varians['+index+'][harga_barang]'" x-model="varian.harga_barang" class="w-full border rounded px-2 py-1 text-sm" required>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs font-medium mb-1">Stok</label>
                        <input type="number" :name="'varians['+index+'][stok_barang]'" x-model="varian.stok_barang" class="w-full border rounded px-2 py-1 text-sm" required>
                    </div>
                    <div class="col-span-1">
                        <button type="button" @click="removeVarian(index)" class="w-full px-2 py-1 bg-red-600 text-white rounded text-xs">Hapus</button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                        <input type="checkbox" x-model="varian.manual_kode" class="rounded">
                        <span>Klik untuk Kode Barang Manual</span>
                    </label>
                </div>
                <div x-show="varian.manual_kode" class="mt-2">
                    <label class="block text-xs font-medium mb-1">Kode Barang (8 digit)</label>
                    <input type="text" :name="'varians['+index+'][kode_barang_manual]'" x-model="varian.kode_barang_manual" class="w-full border rounded px-2 py-1 text-sm font-mono" maxlength="8" placeholder="MN010101">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk auto-generate</p>
                </div>
            </div>
        </template>
        <p x-show="varians.length === 0" class="text-sm text-gray-500 text-center py-4">Belum ada varian. Klik tombol "Tambah Varian" untuk menambahkan.</p>
    </div>
    
    <!-- Vendor & Biaya (lebih sederhana, otomatis pakai stok varian) -->
    <div class="bg-white p-4 rounded shadow max-w-3xl mt-4" x-data="{showVendorBaru:false}">
        <h3 class="font-semibold mb-3">Vendor & Biaya (Opsional)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-1">
                <label class="block text-xs font-medium mb-1">Pilih Vendor</label>
                <select name="vendor_id" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">-- Tidak Ada / Baru --</option>
                    @foreach(\App\Models\Vendor::orderBy('nama')->get() as $v)
                        <option value="{{ $v->id }}">{{ $v->kode ?? 'NO-CODE' }} • {{ $v->nama }}</option>
                    @endforeach
                </select>
                <button type="button" @click="showVendorBaru=!showVendorBaru" class="mt-2 text-xs text-blue-600 hover:underline" x-text="showVendorBaru ? 'Tutup form vendor baru' : 'Tambah vendor baru' "></button>
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-medium mb-1">HPP / Unit</label>
                <input type="number" step="0.01" name="unit_cost" class="w-full border rounded px-3 py-2 text-sm" placeholder="0" value="{{ old('unit_cost') }}">
                <p class="text-xs text-gray-500 mt-1">Dipakai untuk batch awal jika stok > 0</p>
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-medium mb-1">Catatan</label>
                <input type="text" name="notes" class="w-full border rounded px-3 py-2 text-sm" placeholder="Contoh: Stok awal" value="{{ old('notes') }}">
            </div>
        </div>
        <div class="mt-4 border rounded p-3 bg-gray-50" x-show="showVendorBaru">
            <h4 class="font-semibold text-xs mb-2">Data Vendor Baru</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1">Nama Vendor</label>
                    <input type="text" name="vendor_baru" class="w-full border rounded px-3 py-2 text-sm" value="{{ old('vendor_baru') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Kode Vendor</label>
                    <input type="text" name="vendor_kode" class="w-full border rounded px-3 py-2 text-sm" value="{{ old('vendor_kode') }}" placeholder="VND-XXX">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">No Kontak</label>
                    <input type="text" name="vendor_no_kontak" class="w-full border rounded px-3 py-2 text-sm" value="{{ old('vendor_no_kontak') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Alamat</label>
                    <input type="text" name="vendor_alamat" class="w-full border rounded px-3 py-2 text-sm" value="{{ old('vendor_alamat') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Nama Sales</label>
                    <input type="text" name="vendor_nama_sales" class="w-full border rounded px-3 py-2 text-sm" value="{{ old('vendor_nama_sales') }}">
                </div>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3">Stok awal diambil langsung dari kolom "Stok" tiap varian dan otomatis dibuat batch & movement jika nilainya > 0.</p>
    </div>

    <div class="flex gap-2 pt-4">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan</button>
        <a href="{{ route('barang.index') }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</a>
    </div>
</form>

    <!-- Info tambahkan kategori di menu khusus -->
    <div class="bg-amber-50 border-l-4 border-amber-500 p-3 rounded">
        <p class="text-xs text-amber-800">Butuh kategori baru? Tambahkan dari menu Kategori.</p>
    </div>

<script>
function barangForm() {
    return {
        varians: [{jenis: '', kode_jenis: '01', kode_kemasan: '01', ukuran_kemasan: '', harga_barang: 0, stok_barang: 0, manual_kode: false, kode_barang_manual: ''}],
        addVarian() {
            let maxJenis = 0;
            let maxKemasan = 0;
            this.varians.forEach(v => {
                let jenis = parseInt(v.kode_jenis || '0');
                let kemasan = parseInt(v.kode_kemasan || '0');
                if (jenis > maxJenis) maxJenis = jenis;
                if (kemasan > maxKemasan) maxKemasan = kemasan;
            });
            let nextJenis = String(maxJenis).padStart(2, '0');
            let nextKemasan = String(maxKemasan + 1).padStart(2, '0');
            this.varians.push({
                jenis: '',
                kode_jenis: nextJenis,
                kode_kemasan: nextKemasan,
                ukuran_kemasan: '',
                harga_barang: 0,
                stok_barang: 0,
                manual_kode: false,
                kode_barang_manual: ''
            });
        },
        removeVarian(index) {
            if (this.varians.length > 1) {
                this.varians.splice(index, 1);
            } else {
                alert('Minimal harus ada 1 varian');
            }
        }
    }
}
</script>
@endsection
