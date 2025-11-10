@extends('layouts.kasir')
@section('title','Point of Sale')
@section('content')
<div class="flex gap-6" x-data="posApp()">
    <!-- Produk & Search -->
    <div class="flex-1 flex flex-col">
        <div class="flex items-center mb-4 gap-3">
            <h1 class="text-2xl font-semibold">Kasir</h1>
            <div class="flex-1 relative">
                <input type="text" placeholder="Cari barang..." x-model="search" class="w-full border rounded px-3 py-2 text-sm pl-9" />
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
            <select x-model="filterKategori" class="border rounded px-3 py-2 text-sm">
                <option value="">Semua Kategori</option>
                <option value="RK">Rokok</option>
                <option value="MN">Minuman</option>
                <option value="SB">Sembako</option>
                <option value="SK">Snack</option>
                <option value="OB">Obat</option>
                <option value="EK">Elektronik</option>
            </select>
        </div>
        
        <!-- List Barang Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Kategori</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Merk</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Pilih Varian</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Kode</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Harga</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Stok</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($barangGrouped as $group)
                        @php
                            $kategoriLabel = \App\Models\Barang::KATEGORI[$group['kategori']] ?? $group['kategori'];
                            $searchText = strtolower($group['merk']);
                            $totalStok = collect($group['varians'])->sum('stok_barang');
                        @endphp
                        <tr class="hover:bg-gray-50" 
                            x-show="matchFilter(@js($group['kategori']), @js($searchText))"
                            x-data="{ selectedVariant: null, selectedId: '' }">
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700 font-medium">
                                    {{ $kategoriLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $group['merk'] }}</div>
                                <div class="text-xs text-gray-500">{{ count($group['varians']) }} varian • Total: {{ $totalStok }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <select x-model="selectedId" 
                                        @change="selectedVariant = @js($group['varians']).find(v => v.id == $event.target.value)"
                                        class="w-full border rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                                    <option value="">-- Pilih --</option>
                                    @foreach($group['varians'] as $v)
                                        <option value="{{ $v['id'] }}">
                                            {{ $v['jenis'] }} - {{ $v['ukuran_kemasan'] }} ({{ $v['stok_barang'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600 font-mono" x-text="selectedVariant?.kode_barang || '-'"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900" x-show="selectedVariant">
                                    Rp <span x-text="selectedVariant ? new Intl.NumberFormat('id-ID').format(selectedVariant.harga_barang) : '0'"></span>
                                </span>
                                <span class="text-gray-400" x-show="!selectedVariant">-</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium" x-text="selectedVariant?.stok_barang || '-'"></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="selectedVariant && addItem({
                                            barangId: selectedVariant.id, 
                                            nama: '{{ $group['merk'] }} ' + selectedVariant.jenis, 
                                            ukuran: selectedVariant.ukuran_kemasan, 
                                            harga: selectedVariant.harga_barang, 
                                            kode: selectedVariant.kode_barang, 
                                            stok: selectedVariant.stok_barang
                                        }); selectedId = ''; selectedVariant = null;" 
                                        :disabled="!selectedVariant || (selectedVariant?.stok_barang ?? 0) < 1"
                                        class="px-4 py-1.5 text-xs rounded bg-rose-600 text-white hover:bg-rose-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                    + Tambah
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cart -->
    <div class="w-full md:w-80 bg-white rounded-lg shadow flex flex-col max-h-[80vh]">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="font-semibold">Pesanan</h2>
            <button class="text-xs text-gray-500 hover:underline" @click="clearCart()" x-show="items.length">Reset</button>
        </div>
        <div class="flex-1 overflow-y-auto divide-y" x-show="items.length" x-cloak>
            <template x-for="(it,i) in items" :key="it.barangId">
                <div class="p-3 flex gap-3 text-sm">
                    <div class="flex-1">
                        <div class="font-medium" x-text="it.nama"></div>
                        <div class="text-xs text-gray-500" x-text="it.ukuran"></div>
                        <div class="text-xs text-gray-400 mt-0.5" x-text="it.kode"></div>
                        <div class="text-xs mt-1">Rp <span x-text="format(it.harga)"></span> x <span x-text="it.qty"></span></div>
                        <div class="font-semibold text-rose-600 mt-1">Rp <span x-text="format(it.harga*it.qty)"></span></div>
                    </div>
                    <div class="flex flex-col items-center justify-center gap-1">
                        <button class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200" @click="inc(i)">+</button>
                        <span class="text-xs" x-text="it.qty"></span>
                        <button class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200" @click="dec(i)">-</button>
                        <button class="text-[10px] text-red-600 mt-1" @click="remove(i)">hapus</button>
                    </div>
                </div>
            </template>
        </div>
        <div class="p-4 text-sm space-y-1 border-t" x-show="!items.length">
            <p class="text-gray-500 text-xs">Belum ada item.</p>
        </div>
        <div class="p-4 border-t text-sm space-y-1" x-show="items.length" x-cloak>
            <div class="flex justify-between font-semibold text-base"><span>Total</span><span>Rp <span x-text="format(total)"></span></span></div>
            <button class="mt-3 w-full py-2 rounded bg-rose-600 text-white text-sm font-medium disabled:opacity-50" @click="openModal()" :disabled="!items.length">Place Order</button>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md" @click.outside="showModal=false">
            <form @submit.prevent="submitOrder" class="p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg font-semibold">Konfirmasi Pembayaran</h3>
                    <button type="button" @click="showModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="font-medium mb-1 block">Metode</label>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="m in metodeOpts" :key="m.val">
                                <label class="border rounded p-2 flex flex-col items-center gap-1 cursor-pointer text-xs"
                                       :class="metode===m.val ? 'border-rose-500 bg-rose-50' : 'hover:border-gray-400'">
                                    <input type="radio" class="hidden" :value="m.val" x-model="metode">
                                    <span x-text="m.label"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium mb-1 block">Status Pembayaran</label>
                        <select x-model="status" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="lunas">Lunas</option>
                            <option value="belum_lunas">Belum Lunas</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-medium mb-1 block">Nama Customer</label>
                        <input type="text" x-model="namaCustomer" class="w-full border rounded px-3 py-2 text-sm" placeholder="Nama customer (opsional)" maxlength="100">
                    </div>
                    <div>
                        <label class="font-medium mb-1 block">Bayar Tunai</label>
                        <input type="number" x-model="bayarTunai" class="w-full border rounded px-3 py-2 text-sm" placeholder="Masukkan jumlah bayar" min="0" step="1">
                        <div class="text-xs text-gray-500 mt-1" x-show="bayarTunai >= total">
                            Kembalian: Rp <span x-text="format(bayarTunai - total)"></span>
                        </div>
                        <div class="text-xs text-red-500 mt-1" x-show="bayarTunai > 0 && bayarTunai < total">
                             Jumlah bayar kurang dari total
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded p-3 text-xs space-y-1">
                        <div class="flex justify-between font-semibold text-sm"><span>Total</span><span>Rp <span x-text="format(total)"></span></span></div>
                    </div>
                    <p class="text-xs text-gray-500" x-text="message"></p>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" class="px-4 py-2 rounded border text-sm" @click="showModal=false" :disabled="loading">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-rose-600 text-white text-sm font-medium flex-1 disabled:opacity-50" :disabled="loading">
                        <span x-show="!loading">Konfirmasi</span>
                        <span x-show="loading" class="flex items-center gap-2">Memproses <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" opacity="0.25"/><path d="M22 12a10 10 0 0 1-10 10"/></svg></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script defer>
function posApp(){
    return {
        search:'',
        filterKategori:'',
        items:[],
        metode:'cash',
        status:'lunas',
        namaCustomer:'',
        bayarTunai:0,
        metodeOpts:[
            {val:'qris',label:'QRIS'},
            {val:'transfer_bank',label:'Transfer'},
            {val:'cash',label:'Cash'}
        ],
        showModal:false,
        loading:false,
        message:'',
        matchFilter(kategori, text){
            if(this.filterKategori && kategori !== this.filterKategori) return false;
            if(!this.search) return true;
            return text.includes(this.search.toLowerCase());
        },
        addItem(bar){
            if(!bar || (bar.stok ?? 0) < 1){ alert('Stok tidak tersedia'); return; }
            let found=this.items.find(i=>i.barangId===bar.barangId);
            if(found){
                if(found.qty < (bar.stok ?? 0)) found.qty++; else alert('Stok tidak cukup');
            } else {
                this.items.push({
                    barangId: bar.barangId,
                    nama: bar.nama,
                    ukuran: bar.ukuran,
                    harga: bar.harga,
                    kode: bar.kode,
                    stok: bar.stok,
                    qty: 1
                });
            }
        },
        inc(i){ if(this.items[i].qty < (this.items[i].stok ?? Infinity)) this.items[i].qty++; },
        dec(i){ if(this.items[i].qty>1){this.items[i].qty--;} else {this.items.splice(i,1);} },
        remove(i){this.items.splice(i,1);},
        clearCart(){this.items=[];},
        get subtotal(){return this.items.reduce((s,i)=>s+i.harga*i.qty,0);},
        get tax(){return 0;},
        get total(){return this.subtotal;},
        format(n){return new Intl.NumberFormat('id-ID').format(n);},
        openModal(){
            this.showModal=true; 
            this.message='';
            this.bayarTunai = this.total; // Set default to total
        },
        async submitOrder(){
            if(!this.items.length) return;
            
            // Validate bayar_tunai for cash payment
            if(this.metode === 'cash' && this.status === 'lunas') {
                if(!this.bayarTunai || this.bayarTunai < this.total) {
                    this.message = 'Jumlah bayar tunai tidak mencukupi!';
                    return;
                }
            }
            
            this.loading=true; this.message='';
            try{
                const res= await fetch("{{ route('pos.store') }}",{
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
                    body: JSON.stringify({
                        items: this.items.map(i=>({barang_id:i.barangId, qty:i.qty})),
                        metode_transaksi: this.metode,
                        status_transaksi: this.status,
                        bayar_tunai: this.bayarTunai,
                        nama_customer: this.namaCustomer || null
                    })
                });
                if(!res.ok){
                    let text= await res.text();
                    throw new Error(text);
                }
                const data = await res.json();
                this.message='Transaksi berhasil!';
                
                // Redirect to nota if lunas
                if(this.status === 'lunas' && data.data && data.data.transaksi_id) {
                    setTimeout(() => {
                        window.location.href = "{{ url('/pos/nota') }}/" + data.data.transaksi_id;
                    }, 500);
                } else {
                    this.clearCart();
                    setTimeout(()=>{this.showModal=false;},1000);
                }
            }catch(e){
                this.message = 'Gagal: '+ (e.message.slice(0,180));
            }finally{this.loading=false;}
        }
    }
}
</script>
@endsection
