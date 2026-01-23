@extends('layouts.kasir')
@section('title','Tambah Barang')
@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="max-w-6xl mx-auto" x-data="barangForm()">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 text-gray-500 text-sm">
            <a href="{{ route('barang.index') }}" class="hover:text-blue-600">Barang</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Tambah Barang & Varian</span>
        </div>
        <a href="{{ route('barang.index') }}" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Form Input Barang Baru (Multi Varian)
            </h2>
            <p class="text-blue-100 text-sm mt-1">Isi informasi merk, lalu tambahkan varian sebanyak yang diperlukan. Kode otomatis ter-generate!</p>
        </div>

        <form action="{{ route('barang.store') }}" method="POST" class="p-6">
            @csrf
            
            <!-- Info Banner -->
            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <div>
                        <h3 class="font-bold text-blue-900 mb-1">Format Kode Otomatis (9 Karakter)</h3>
                        <p class="text-sm text-blue-700"><strong>XX</strong> (Kategori) + <strong>000</strong> (Hash Merk) + <strong>00</strong> (Hash Varian) + <strong>00</strong> (Hash Kemasan)</p>
                        <p class="text-xs text-blue-600 mt-1">Contoh: <span class="font-mono font-bold">RK4521267</span> → RK = Rokok, 452 = Surya, 12 = Merah, 67 = 20btg</p>
                    </div>
                </div>
            </div>

            <!-- Base Info (Kategori, Merk, Satuan) -->
            <div class="mb-6 p-5 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-1 h-6 bg-blue-600 rounded"></span>
                    Informasi Dasar (Berlaku untuk semua varian)
                </h3>
                
                <!-- Hidden inputs for base data -->
                <input type="hidden" name="kategori" x-model="kategori">
                <input type="hidden" name="merk" x-model="merk">
                <input type="hidden" name="satuan" x-model="satuan">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select x-model="kategori" @change="updateAllCodes()" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none" required>
                            <option value="">-- Pilih --</option>
                            <option value="RK">RK - Rokok</option>
                            <option value="SB">SB - Sembako</option>
                            <option value="MN">MN - Minuman</option>
                            <option value="SK">SK - Snack</option>
                            <option value="OB">OB - Obat</option>
                            <option value="EK">EK - Elektronik</option>
                            <option value="LL">LL - Lain-lain</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Merk/Brand <span class="text-red-500">*</span></label>
                        <input type="text" x-model="merk" @input="updateAllCodes()" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none" placeholder="Contoh: Indomie, Surya, Aqua..." required>
                        <p class="text-xs text-gray-500 mt-1">Hash: <span class="font-mono font-bold text-purple-600" x-text="merkCode"></span></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" x-model="satuan" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none" placeholder="pcs, kg, box..." required>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-300">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Vendor / Supplier 
                        <span class="text-gray-500 font-normal">(Opsional - untuk tracking kulakan)</span>
                    </label>
                    <input type="hidden" name="vendor_id" x-model="vendorId">
                    <select x-model="vendorId" class="w-full md:w-1/2 border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                        <option value="">-- Pilih Vendor (atau kosongkan) --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }} - {{ $vendor->hp ?? 'No Phone' }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">💡 Jika diisi, transaksi MASUK dari kulakan akan tercatat dengan vendor ini</p>
                </div>
            </div>

            <!-- Varian Table -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-1 h-6 bg-green-600 rounded"></span>
                        Daftar Varian (Minimal 1)
                    </h3>
                    <button type="button" @click="addVariant()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm flex items-center gap-2 shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Varian
                    </button>
                </div>
                
                <div class="overflow-x-auto border-2 border-gray-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold w-8">No</th>
                                <th class="px-4 py-3 text-left font-bold">Varian/Jenis</th>
                                <th class="px-4 py-3 text-left font-bold">Isi/Kemasan</th>
                                <th class="px-4 py-3 text-left font-bold">Harga Beli (Rp)</th>
                                <th class="px-4 py-3 text-left font-bold">Harga Jual (Rp)</th>
                                <th class="px-4 py-3 text-left font-bold">Stok Awal</th>
                                <th class="px-4 py-3 text-center font-bold w-44">Kode Auto</th>
                                <th class="px-4 py-3 text-center font-bold w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <template x-for="(v, index) in varians" :key="index">
                                <tr class="hover:bg-blue-50 transition">
                                    <!-- Hidden inputs for each variant -->
                                    <input type="hidden" :name="`varians[${index}][varian]`" x-model="v.varian">
                                    <input type="hidden" :name="`varians[${index}][kemasan]`" x-model="v.kemasan">
                                    <input type="hidden" :name="`varians[${index}][kode]`" x-model="v.kode">
                                    <input type="hidden" :name="`varians[${index}][harga_beli]`" x-model="v.hargaBeli">
                                    <input type="hidden" :name="`varians[${index}][harga]`" x-model="v.harga">
                                    <input type="hidden" :name="`varians[${index}][stok]`" x-model="v.stok">
                                    
                                    <td class="px-4 py-3 text-center text-gray-600 font-bold" x-text="index + 1"></td>
                                    <td class="px-4 py-3">
                                        <input type="text" x-model="v.varian" @input="updateCode(index)" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none" placeholder="Merah, Goreng, XL..." required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" x-model="v.kemasan" @input="updateCode(index)" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none" placeholder="20btg, 250ml...">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" x-model="v.hargaBeli" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none bg-yellow-50" placeholder="0">
                                        <p class="text-xs text-gray-500 mt-0.5">Kulakan</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" x-model="v.harga" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none" placeholder="0" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.001" x-model="v.stok" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none" placeholder="0">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-mono font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded border border-blue-200" x-text="v.kode || 'XX0000000'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="removeVariant(index)" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition" :disabled="varians.length === 1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('barang.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold flex items-center gap-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Semua Varian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function barangForm() {
    return {
        kategori: '',
        merk: '',
        satuan: 'pcs',
        vendorId: '',
        merkCode: '000',
        varians: [
            { varian: '', kemasan: '', hargaBeli: '', harga: '', stok: 0, kode: '' }
        ],

        addVariant() {
            this.varians.push({ varian: '', kemasan: '', hargaBeli: '', harga: '', stok: 0, kode: '' });
        },

        removeVariant(index) {
            if (this.varians.length > 1) {
                this.varians.splice(index, 1);
            }
        },

        updateAllCodes() {
            this.merkCode = this.hashToDigits(this.merk, 3);
            this.varians.forEach((v, i) => this.updateCode(i));
        },

        updateCode(index) {
            let v = this.varians[index];
            let varCode = this.hashToDigits(v.varian, 2);
            let kemCode = this.hashToDigits(v.kemasan, 2);
            v.kode = (this.kategori || 'XX') + this.merkCode + varCode + kemCode;
        },

        hashToDigits(str, length) {
            if (!str || str.trim() === '') return '0'.repeat(length);
            
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                hash = ((hash << 5) - hash) + str.charCodeAt(i);
                hash = hash & hash;
            }
            
            let result = Math.abs(hash).toString().substring(0, length);
            while (result.length < length) {
                result = '0' + result;
            }
            
            return result;
        }
    }
}
</script>
@endsection
