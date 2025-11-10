# Perubahan Sistem Kasir - Update Terbaru

## Ringkasan Perubahan

Sistem kasir telah diperbarui dengan fitur-fitur baru:

1. **Kategori Barang** - Setiap barang sekarang memiliki kategori
2. **Kode Barang Otomatis** - Format: `KATEGORI-KODEMERK-INDEXUKURAN`
3. **Multi Ukuran/Kemasan** - Satu barang dapat memiliki beberapa varian ukuran/kemasan
4. **View POS Compact** - Tampilan POS dengan dropdown pilihan ukuran (lebih rapi)
5. **Filter Kategori & Tanggal** - Filter di POS dan halaman barang
6. **Manajemen Stok Per Detail** - Stok dikelola per ukuran/kemasan

---

## Kategori Barang

```
RK = Rokok
MN = Minuman
SB = Sembako
SK = Snack
OB = Obat
EK = Elektronik
```

---

## Format Kode Barang (REVISI TERBARU)

### Format Kode Detail: `KATEGORI-KODEMERK-INDEXUKURAN`

**Penjelasan:**
- **KATEGORI**: 2 huruf (RK, MN, SB, SK, OB, EK)
- **KODEMERK**: 3 digit angka (001, 002, 003...) - Auto increment per kategori
- **INDEXUKURAN**: 1-2 digit angka (1, 2, 3...) - Index ukuran dalam barang yang sama

**Contoh:**
```
RK-001-1    = Rokok merk pertama, ukuran 1 (1 Bungkus)
RK-001-2    = Rokok merk pertama, ukuran 2 (1 Slop)
MN-001-1    = Minuman merk pertama, ukuran 1 (330ml)
MN-001-2    = Minuman merk pertama, ukuran 2 (600ml)
MN-001-3    = Minuman merk pertama, ukuran 3 (1500ml)
SB-001-1    = Sembako merk pertama, ukuran 1 (5 Kg)
SB-001-2    = Sembako merk pertama, ukuran 2 (10 Kg)
SK-001-1    = Snack merk pertama, ukuran 1
```

**Keuntungan Format Baru:**
- ✅ Lebih ringkas dan mudah dibaca
- ✅ Mudah untuk tracking per merk (kode_merk)
- ✅ Index ukuran terstruktur (1, 2, 3...)
- ✅ Tidak ada duplikasi nama barang di kode

---

## Struktur Database

### Tabel: `barang`
- `id` - Primary key
- `kode_barang` - Kode barang (KATEGORI-KODEMERK) contoh: RK-001
- `kategori` - Kategori (RK/MN/SB/SK/OB/EK)
- `kode_merk` - Kode angka merk (001, 002, 003...) auto increment per kategori
- `nama_barang` - Nama barang
- `deskripsi` - Deskripsi barang
- `created_at` - Tanggal ditambahkan
- `updated_at` - Tanggal diupdate

### Tabel: `barang_details` (Detail Ukuran/Kemasan)
- `id` - Primary key
- `barang_id` - Foreign key ke tabel barang
- `index_ukuran` - Index ukuran (1, 2, 3...) auto increment per barang
- `kode_detail` - Kode lengkap (KATEGORI-KODEMERK-INDEXUKURAN)
- `ukuran_kemasan` - Ukuran/kemasan (contoh: "1 Liter", "Box 12 pcs")
- `harga` - Harga untuk ukuran ini
- `stok` - Stok untuk ukuran ini

### Tabel: `transaksi`
- Menggunakan `barang_detail_id` (referensi ke barang_details.id)
- Stok dikurangi dari `barang_details`, bukan dari `barang`

---

## Perubahan Tampilan POS (Point of Sale)

### Sebelum:
- List panjang dengan setiap ukuran ditampilkan per baris
- Tidak efisien untuk barang dengan banyak varian

### Sesudah (REVISI):
- **List per barang** (bukan per ukuran)
- **Dropdown untuk pilih ukuran** di setiap baris
- Lebih compact dan rapi
- Menampilkan: Kode Barang, Nama, Kategori, Dropdown Ukuran, Tombol Tambah

**Fitur:**
- ✅ Filter kategori dengan dropdown
- ✅ Search berdasarkan nama barang
- ✅ Pilih ukuran dari dropdown dengan info harga & stok
- ✅ Validasi stok otomatis
- ✅ Tampilan lebih rapi dan efisien

---

## Perubahan Halaman Data Barang

### Filter Baru:
1. **Filter Kategori** - Pilih kategori dari dropdown
2. **Filter Tanggal Mulai** - Lihat barang yang ditambahkan mulai tanggal tertentu
3. **Filter Tanggal Akhir** - Lihat barang yang ditambahkan sampai tanggal tertentu
4. **Tombol Reset** - Reset semua filter

**Kegunaan:**
- 📅 Melihat barang yang ditambahkan bulan ini
- 📅 Melihat barang yang ditambahkan minggu kemarin
- 📅 Tracking inventory berdasarkan waktu penambahan
- 📦 Filter per kategori untuk audit stok

### Tampilan:
- Menampilkan tanggal barang ditambahkan
- Detail ukuran dengan kode lengkap (KATEGORI-KODEMERK-INDEXUKURAN)
- Harga dan stok per ukuran

---

## Cara Menggunakan

### 1. Menambah Barang Baru
1. Pilih **kategori** dari dropdown (RK, MN, SB, SK, OB, EK)
2. Masukkan **nama barang**
3. Masukkan **deskripsi** (opsional)
4. Klik "**Tambah Detail**" untuk menambah varian ukuran
5. Untuk setiap detail, isi:
   - Ukuran/Kemasan (contoh: "1 Liter", "Box 12 pcs", "Sachet 10g")
   - Harga
   - Stok
6. Simpan

**Hasil:**
- Kode merk akan otomatis (contoh: 001, 002, 003...)
- Setiap detail akan mendapat index_ukuran otomatis (1, 2, 3...)
- Kode detail akan terbentuk: KATEGORI-KODEMERK-INDEXUKURAN

### 2. Edit Barang
1. Detail existing ditampilkan dengan background **biru**
2. Detail baru ditampilkan dengan background **abu-abu**
3. Bisa tambah/hapus detail ukuran
4. Perubahan stok langsung mempengaruhi detail tersebut

### 3. Filter Data Barang
1. **Filter Kategori**: Pilih kategori dari dropdown
2. **Filter Tanggal**: Isi tanggal mulai dan/atau tanggal akhir
3. Klik "**Filter**"
4. Untuk reset filter, klik "**Reset**"

**Contoh Penggunaan:**
- Lihat semua barang **Minuman** yang ditambahkan **bulan ini**
- Lihat barang **Sembako** yang ditambahkan **minggu lalu**
- Audit stok berdasarkan tanggal penambahan

### 4. Transaksi di POS
1. Gunakan **filter kategori** untuk mempersempit pilihan
2. **Cari barang** dengan search box
3. **Pilih ukuran** dari dropdown di kolom "Pilih Ukuran"
4. Klik "**+ Tambah**"
5. Barang masuk ke keranjang dengan info nama, ukuran, kode
6. Proses pembayaran seperti biasa

**Keuntungan:**
- Lebih cepat memilih barang
- Tidak perlu scroll panjang
- Langsung lihat semua ukuran tersedia
- Info harga & stok langsung di dropdown

---

## Migrasi dari Versi Lama

Jika Anda memiliki data lama, jalankan:

```bash
# 1. Backup database terlebih dahulu!
# 2. Jalankan migrasi
php artisan migrate

# 3. (Opsional) Clear dan reseed data
php artisan tinker --execute="DB::table('barang')->delete(); DB::table('barang_details')->delete();"
php artisan db:seed --class=BarangSeeder
```

---

## Sample Data

Seeder menyediakan 9 barang contoh dengan berbagai kategori:

| Kategori | Nama Barang | Kode Merk | Jumlah Ukuran |
|----------|-------------|-----------|---------------|
| RK | Sampoerna Mild | 001 | 2 varian |
| MN | Aqua | 001 | 3 varian |
| MN | Teh Botol Sosro | 002 | 2 varian |
| SB | Beras Premium | 001 | 3 varian |
| SB | Gula Pasir | 002 | 2 varian |
| SK | Chitato | 001 | 3 varian |
| SK | Indomie Goreng | 002 | 2 varian |
| OB | Paracetamol | 001 | 2 varian |
| EK | Baterai ABC | 001 | 2 varian |

**Total:** 9 barang induk, 22 detail produk

---

## File yang Dimodifikasi

### Migrations (Baru)
- `2025_10_12_124353_create_barang_details_table.php`
- `2025_10_12_124446_add_kategori_to_barang_table.php`
- `2025_10_12_124624_update_transaksi_to_use_barang_detail.php`
- `2025_10_12_131923_add_kode_merk_and_index_ukuran.php` ⭐ NEW

### Models (Updated)
- `app/Models/Barang.php` - Added kode_merk, auto-generate kode
- `app/Models/BarangDetail.php` - Added index_ukuran, new kode format
- `app/Models/Transaksi.php` - Use barang_detail_id

### Controllers (Updated)
- `app/Http/Controllers/KasirController.php` - Handle detail selection
- `app/Http/Controllers/BarangController.php` - Date filtering, index_ukuran

### Views (Updated)
- `resources/views/pos/index.blade.php` - ⭐ Compact list with dropdown
- `resources/views/barang/index.blade.php` - ⭐ Date range filter
- `resources/views/barang/create.blade.php` - Handle new fields
- `resources/views/barang/edit.blade.php` - Handle new fields

### Seeders
- `database/seeders/BarangSeeder.php` - Sample data

---

## Perbandingan Format Kode

| Versi | Format | Contoh | Keterangan |
|-------|--------|--------|------------|
| **Lama** | KATEGORI-NAMABARANG-UKURAN | RK-SAMPOERNAMI-1BUNGKUS | Panjang, sulit dibaca |
| **Baru** ⭐ | KATEGORI-KODEMERK-INDEXUKURAN | RK-001-1 | Ringkas, terstruktur |

---

## Update Log

### Version 2.0 (12 Oktober 2025)
- ✅ Format kode baru: KATEGORI-KODEMERK-INDEXUKURAN
- ✅ POS view compact dengan dropdown ukuran
- ✅ Filter tanggal di data barang
- ✅ Auto-generate kode_merk per kategori
- ✅ Auto-increment index_ukuran per barang

### Version 1.0 (12 Oktober 2025)
- ✅ Sistem kategori barang
- ✅ Multi ukuran/kemasan
- ✅ POS berbasis list
- ✅ Filter kategori
- ✅ Manajemen stok per detail

---

## Kontak & Support

Jika ada pertanyaan atau issue, silakan hubungi developer.

---

**Terakhir diupdate:** 12 Oktober 2025
