@extends('layouts.kasir')
@section('title','Edit Barang')
@section('content')
<h1 class="text-xl font-semibold mb-4">Edit Barang</h1>
<form action="{{ route('barang.update',$barang) }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow max-w-3xl" x-data="barangForm()">
    @csrf @method('PUT')
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
                        <option value="{{ $k }}" {{ old('kategori', $barang->kategori) == $k ? 'selected' : '' }}>{{ $label }} ({{ $k }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nama Merk Barang</label>
                <input type="text" name="merk" value="{{ old('merk',$barang->merk) }}" class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Deskripsi</label>
        <textarea name="deskripsi" rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('deskripsi',$barang->deskripsi) }}</textarea>
    </div>
    
    <hr class="my-4">
    <div class="flex justify-between items-center mb-2">
        <h3 class="font-medium">Varian Jenis & Kemasan</h3>
        <button type="button" @click="addVarian()" class="text-sm px-3 py-1 bg-green-600 text-white rounded">+ Tambah Varian</button>
    </div>
    
    <div class="space-y-3">
        <template x-for="(varian, index) in varians" :key="index">
            <div class="border p-3 rounded" :class="varian.id ? 'bg-blue-50' : 'bg-gray-50'">
                <input type="hidden" :name="'varians['+index+'][id]'" x-model="varian.id">
                <div class="grid grid-cols-12 gap-3 items-end mb-2">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Nama Jenis</label>
                        <input type="text" :name="'varians['+index+'][jenis]'" x-model="varian.jenis" class="w-full border rounded px-2 py-1 text-sm" required placeholder="Botol">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Kode Jenis</label>
                        <input type="text" :name="'varians['+index+'][kode_jenis]'" x-model="varian.kode_jenis" class="w-full border rounded px-2 py-1 text-sm" maxlength="2" placeholder="01">
                        <p class="text-xs text-gray-400">2 digit (01-99)</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Kode Kemasan</label>
                        <input type="text" :name="'varians['+index+'][kode_kemasan]'" x-model="varian.kode_kemasan" class="w-full border rounded px-2 py-1 text-sm" maxlength="2" placeholder="01">
                        <p class="text-xs text-gray-400">2 digit (01-99)</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1">Ukuran/Kemasan</label>
                        <input type="text" :name="'varians['+index+'][ukuran_kemasan]'" x-model="varian.ukuran_kemasan" class="w-full border rounded px-2 py-1 text-sm" required>
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
                <div class="flex items-center gap-2 mb-2">
                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                        <input type="checkbox" x-model="varian.manual_kode" class="rounded">
                        <span>Override Kode Barang Manual</span>
                    </label>
                    <span x-show="varian.id && !varian.manual_kode" class="text-xs text-gray-500">
                        Kode Saat Ini: <span class="font-mono" x-text="varian.kode_barang"></span>
                    </span>
                </div>
                <div x-show="varian.manual_kode">
                    <label class="block text-xs font-medium mb-1">Kode Barang (8 digit)</label>
                    <input type="text" :name="'varians['+index+'][kode_barang_manual]'" x-model="varian.kode_barang_manual" class="w-full border rounded px-2 py-1 text-sm font-mono" maxlength="8" placeholder="MN010101">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk auto-generate dari kode jenis & kemasan</p>
                </div>
            </div>
        </template>
        <p x-show="varians.length === 0" class="text-sm text-gray-500 text-center py-4">Belum ada varian.</p>
    </div>
    
    <div class="flex gap-2 pt-4">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Update</button>
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
        varians: {!! json_encode($varians->map(function($v) {
            // Extract kode_jenis (posisi 5-6 untuk legacy 9-digit; 4-5 untuk 8-digit). Gunakan offset dinamis.
            $len = strlen($v->kode_barang);
            if ($len >= 8) {
                // New 8-digit: KK(2) + MM(2) + JJ(2) + KKEM(2)
                $kodeJenis = substr($v->kode_barang, 4, 2);
                $kodeKemasan = substr($v->kode_barang, 6, 2);
            } else {
                $kodeJenis = '';
                $kodeKemasan = '';
            }
            
            return [
                'id' => $v->id,
                'jenis' => $v->jenis,
                'kode_jenis' => $kodeJenis,
                'kode_kemasan' => $kodeKemasan,
                'ukuran_kemasan' => $v->ukuran_kemasan,
                'harga_barang' => $v->harga_barang,
                'stok_barang' => $v->stok_barang,
                'kode_barang' => $v->kode_barang,
                'manual_kode' => false,
                'kode_barang_manual' => ''
            ];
        })->values()) !!},
        addVarian() {
            // Find max kode_jenis and kode_kemasan for auto increment
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
                id: null, 
                jenis: '', 
                kode_jenis: nextJenis, 
                kode_kemasan: nextKemasan, 
                ukuran_kemasan: '', 
                harga_barang: 0, 
                stok_barang: 0, 
                kode_barang: '', 
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
