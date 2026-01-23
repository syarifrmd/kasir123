@extends('layouts.kasir')
@section('title','Point of Sale')
@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-200px)] lg:h-[85vh]" x-data="posApp()">
    <!-- Produk & Search -->
    <div class="flex-1 flex flex-col h-full">
        <!-- Controls -->
        <div class="flex items-center mb-4 gap-3 bg-white p-3 rounded shadow-sm">
            <h1 class="text-xl font-bold text-gray-800">Kasir</h1>
            <div class="flex-1 relative">
                <input type="text" placeholder="Cari barang (nama, kode, merk)..." x-model="search" class="w-full border rounded-lg px-3 py-2 text-sm pl-9 focus:ring-2 focus:ring-blue-500 outline-none" />
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
            <select x-model="filterKategori" class="border rounded-lg px-3 py-2 text-sm bg-gray-50">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- List Barang Table (Scrollable Area) -->
        <div class="bg-white rounded-lg shadow flex-1 overflow-hidden flex flex-col border border-gray-200">
            <div class="overflow-y-auto flex-1">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 w-1/4">Barang</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 w-1/4">Varian</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 w-1/6">Harga</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700 w-1/6">Stok</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700 w-1/6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="group in groupedItems" :key="group.key">
                            <tr class="hover:bg-blue-50 transition group" x-data="{ selectedId: group.variants[0].id }">
                                <!-- Barang / Merk -->
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-800" x-text="group.merk"></div>
                                    <div class="text-xs text-gray-500 mt-1" x-show="group.variants.length === 1">
                                        <span class="bg-gray-100 px-1.5 py-0.5 rounded border" x-text="group.variants[0].kode_barang"></span>
                                    </div>
                                    <!-- Show active code for group -->
                                    <div class="text-xs text-gray-500 mt-1" x-show="group.variants.length > 1">
                                        <span class="bg-gray-100 px-1.5 py-0.5 rounded border" x-text="group.variants.find(v => v.id == selectedId)?.kode_barang"></span>
                                    </div>
                                </td>

                                <!-- Varian Selection -->
                                <td class="px-4 py-3">
                                    <!-- Single Item -->
                                    <div x-show="group.variants.length === 1">
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-orange-100 text-orange-800" 
                                              x-show="group.variants[0].ukuran" 
                                              x-text="group.variants[0].ukuran"></span>
                                        <span class="text-gray-400 text-xs" x-show="!group.variants[0].ukuran">-</span>
                                    </div>

                                    <!-- Multiple Variants (Dropdown) -->
                                    <div x-show="group.variants.length > 1">
                                        <select x-model="selectedId" 
                                                class="w-full text-xs border border-gray-300 rounded px-2 py-1.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none cursor-pointer hover:border-blue-400 transition">
                                            <template x-for="v in group.variants" :key="v.id">
                                                <option :value="v.id" x-text="getVariantLabel(v, group.merk)"></option>
                                            </template>
                                        </select>
                                    </div>
                                </td>

                                <!-- Harga (Reactive to Selection) -->
                                <td class="px-4 py-3 font-mono font-medium text-gray-700">
                                    <span x-text="format(group.variants.find(v => v.id == selectedId)?.harga_jual || 0)"></span>
                                </td>

                                <!-- Stok (Reactive to Selection) -->
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-bold px-2 py-1 rounded-full"
                                          :class="(group.variants.find(v => v.id == selectedId)?.stok > 0) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                        <span x-text="Number(group.variants.find(v => v.id == selectedId)?.stok || 0)"></span>
                                    </span>
                                </td>

                                <!-- Aksi (Add Selected) -->
                                <td class="px-4 py-3 text-center">
                                    <button @click="addItem(group.variants.find(v => v.id == selectedId))" 
                                            :disabled="(group.variants.find(v => v.id == selectedId)?.stok || 0) <= 0"
                                            class="px-3 py-1.5 text-xs font-bold rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-sm flex items-center gap-1 mx-auto">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredItems.length === 0" class="bg-gray-50">
                            <td colspan="5" class="py-8 text-center text-gray-500">Barang tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Side: Cart -->
    <div class="w-full lg:w-[380px] bg-white rounded-lg shadow border border-gray-200 flex flex-col h-full">
        <div class="p-4 border-b bg-gray-800 text-white rounded-t-lg flex justify-between items-center">
            <h2 class="font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Pesanan
            </h2>
            <button class="text-xs bg-red-500 hover:bg-red-600 px-2 py-1 rounded transition text-white" @click="clearCart()" x-show="cart.length">Reset</button>
        </div>

        <!-- Form Pembeli -->
        <div class="p-4 border-b bg-gray-50 space-y-3">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Pelanggan</label>
                <div class="flex gap-2">
                    <select x-model="selectedCustomer" class="flex-1 w-full border rounded px-2 py-1.5 text-sm bg-white focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Umum / Walk-in --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-name="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div x-show="!selectedCustomer">
                <input type="text" x-model="tempName" class="w-full border rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-500" placeholder="Nama Pembeli (Langsung)...">
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-2 bg-white space-y-2">
            <template x-for="(item, index) in cart" :key="index">
                <div class="p-3 border rounded-lg bg-gray-50 flex gap-3 relative group hover:border-blue-300 transition-colors">
                    <div class="flex-1">
                        <div class="font-bold text-sm text-gray-800 line-clamp-2" x-text="item.nama_barang"></div>
                        <div class="text-xs text-gray-500 mt-1 flex gap-1 items-center">
                            <span x-show="item.merk" x-text="item.merk"></span>
                            <span x-show="item.ukuran" class="bg-gray-200 px-1 rounded text-[10px]" x-text="item.ukuran"></span>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <div class="text-blue-600 font-medium text-xs">
                                <span x-text="format(item.harga_jual)"></span> x <span x-text="item.qty"></span>
                            </div>
                            <div class="font-bold text-gray-800 text-sm">
                                <span x-text="format(item.harga_jual * item.qty)"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Qty Controls -->
                    <div class="flex flex-col items-center justify-center gap-1 ml-2">
                        <button class="w-6 h-6 rounded bg-white border hover:bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm" @click="updateQty(index, 1)">+</button>
                        <input type="number" x-model="item.qty" class="w-10 text-center text-xs border-none bg-transparent font-bold p-0 appearance-none" readonly>
                        <button class="w-6 h-6 rounded bg-white border hover:bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm" @click="updateQty(index, -1)">-</button>
                    </div>

                    <!-- Remove Button -->
                    <button class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition shadow-md hover:bg-red-600" @click="removeFromCart(index)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </template>
            
            <div x-show="cart.length === 0" class="flex flex-col items-center justify-center h-40 text-gray-400 text-sm">
                <svg class="w-10 h-10 mb-2 opacity-20" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/></svg>
                <p>Keranjang kosong</p>
                <p class="text-xs">Pilih barang disamping</p>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="p-4 border-t bg-gray-50 rounded-b-lg">
            <div class="flex justify-between items-center mb-3">
                <span class="text-gray-600 font-bold">Total</span>
                <span class="text-2xl font-bold text-blue-700" x-text="format(total)"></span>
            </div>

            <button type="button" @click="openPaymentModal()" :disabled="cart.length === 0" class="w-full py-3 bg-gray-800 hover:bg-black text-white font-bold rounded-lg shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition">
                <span>BAYAR SEKARANG</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak x-transition.opacity>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.outside="showModal=false">
            <div class="p-5 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-800">Konfirmasi Pembayaran</h3>
                <button @click="showModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <form action="{{ route('transaksi.store') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <!-- Hidden Inputs -->
                <input type="hidden" name="tanggal" value="{{ now()->format('Y-m-d\TH:i') }}">
                <input type="hidden" name="kontak_id" x-model="selectedCustomer">
                <input type="hidden" name="new_customer_name" x-model="tempName">
                
                <template x-for="(item, idx) in cart" :key="idx">
                    <div>
                        <input type="hidden" :name="`items[${idx}][barang_id]`" :value="item.id">
                        <input type="hidden" :name="`items[${idx}][qty]`" :value="item.qty">
                    </div>
                </template>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_bayar" value="CASH" class="peer sr-only" x-model="metode" checked>
                            <div class="border-2 rounded-lg p-3 text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 hover:bg-gray-50 transition">
                                <div class="font-bold">CASH</div>
                                <div class="text-xs text-gray-500">Tunai</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_bayar" value="QRIS" class="peer sr-only" x-model="metode">
                            <div class="border-2 rounded-lg p-3 text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 hover:bg-gray-50 transition">
                                <div class="font-bold">QRIS</div>
                                <div class="text-xs text-gray-500">Scan Code</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="metode === 'CASH'">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Uang Diterima (Rp)</label>
                    <input type="number" x-model="bayar" class="w-full border-2 rounded-lg px-4 py-2 text-lg font-bold text-right focus:border-blue-500 focus:ring-0 outline-none" placeholder="0">
                    
                    <div class="mt-3 p-3 bg-gray-50 rounded border flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Kembalian:</span>
                        <span class="font-bold text-xl" :class="kembalian < 0 ? 'text-red-500' : 'text-green-600'" x-text="format(kembalian > 0 ? kembalian : 0)"></span>
                    </div>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="showModal=false" class="flex-1 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50">Batal</button>
                    <button type="submit" 
                            :disabled="metode === 'CASH' && kembalian < 0"
                            class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow disabled:opacity-50 disabled:cursor-not-allowed">
                        Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function posApp() {
        return {
            allItems: @json($barang),
            search: '',
            filterKategori: '',
            cart: [],
            selectedCustomer: '', // ID from DB
            tempName: '', // Manual Name
            showModal: false,
            metode: 'CASH',
            bayar: '',

            get filteredItems() {
                const q = this.search.toLowerCase();
                const cat = this.filterKategori;
                
                return this.allItems.filter(i => {
                    const matchesSearch = !q || 
                        (i.nama_barang && i.nama_barang.toLowerCase().includes(q)) || 
                        (i.kode_barang && i.kode_barang.toLowerCase().includes(q)) ||
                        (i.merk && i.merk.toLowerCase().includes(q));
                    const matchesCat = !cat || i.kategori === cat;
                    return matchesSearch && matchesCat;
                });
            },

            get groupedItems() {
                const groups = {};
                const result = [];
                
                this.filteredItems.forEach(item => {
                    // Normalize key: Use merk if exists, otherwise treat as unique
                    let key = (item.merk && item.merk !== '-') ? item.merk.toLowerCase().trim() : '___ID_' + item.id;
                    let displayMerk = (item.merk && item.merk !== '-') ? item.merk : item.nama_barang;

                    if (!groups[key]) {
                        groups[key] = {
                            key: key,
                            merk: displayMerk, // Display Name
                            variants: []
                        };
                        result.push(groups[key]);
                    }
                    groups[key].variants.push(item);
                });

                return result;
            },

            getVariantLabel(item, merkName) {
                // Return 'nama_barang' minus 'merk', or 'ukuran'
                if (item.merk && item.nama_barang.toLowerCase().startsWith(item.merk.toLowerCase())) {
                    let suffix = item.nama_barang.substring(item.merk.length).trim();
                    if (suffix) return suffix + (item.ukuran ? ` (${item.ukuran})` : '');
                }
                return item.ukuran || item.nama_barang;
            },

            get total() {
                return this.cart.reduce((sum, i) => sum + (i.harga_jual * i.qty), 0);
            },

            get kembalian() {
                return (this.bayar || 0) - this.total;
            },

            addItem(item) {
                let existing = this.cart.find(c => c.id === item.id);
                if (existing) {
                    if (existing.qty < item.stok) {
                        existing.qty++;
                    } else {
                        alert('Stok tidak cukup!');
                    }
                } else {
                    this.cart.push({ ...item, qty: 1 });
                }
            },

            updateQty(index, change) {
                let item = this.cart[index];
                let newQty = item.qty + change;
                if (newQty > 0 && newQty <= item.stok) {
                    item.qty = newQty;
                } else if (newQty > item.stok) {
                    alert('Mencapai batas stok tersedia!');
                } else {
                    this.removeFromCart(index);
                }
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
            },

            clearCart() {
                this.cart = [];
                this.bayar = '';
            },

            openPaymentModal() {
                this.showModal = true;
                this.bayar = ''; // Reset payment input
            },

            format(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }
        }
    }
</script>
@endsection
