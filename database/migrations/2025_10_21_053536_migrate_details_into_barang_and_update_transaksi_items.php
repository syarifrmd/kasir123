<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Add barang_id to transaksi_items if not exists
        if (!Schema::hasColumn('transaksi_items', 'barang_id')) {
            Schema::table('transaksi_items', function (Blueprint $table) {
                $table->unsignedBigInteger('barang_id')->nullable()->after('id');
                $table->index('barang_id');
            });
        }

        // 2) Copy rows: For each barang_details row, create/ensure a flattened barang row, and map transaksi_items
        // This part uses DB facade for raw operations
        $barangs = DB::table('barang')->get()->keyBy('id');
        $details = DB::table('barang_details')->orderBy('id')->get();

        foreach ($details as $detail) {
            $parent = $barangs[$detail->barang_id] ?? null;
            if (!$parent) continue;

            // Compute codes
            $kategori = $parent->kategori ?? 'SK';
            $kodeMerk = str_pad((string)intval($parent->kode_merk ?: 0), 3, '0', STR_PAD_LEFT);
            $kodeJenis = '01'; // default jenis mapping, can be refined
            $kodeKemasan = str_pad((string)intval($detail->index_ukuran ?: 0), 2, '0', STR_PAD_LEFT);
            $kodeBarang = $kategori . $kodeMerk . $kodeJenis . $kodeKemasan; // 9 chars (2+3+2+2)

            // If this detail already migrated, reuse its barang id
            $existing = DB::table('barang')
                ->where('merk', $parent->nama_barang)
                ->where('ukuran_kemasan', $detail->ukuran_kemasan)
                ->first();
            if ($existing) {
                $newBarangId = $existing->id;
            } else {
                // Avoid duplicate kode by skipping if kode already exists
                $dup = DB::table('barang')->where('kode_barang', $kodeBarang)->first();
                if ($dup) {
                    // If duplicate by kode exists but not tied to this detail, try adjust kemasan code to next available
                    $seq = 1;
                    do {
                        $kodeKemasanAlt = str_pad((string)$seq, 2, '0', STR_PAD_LEFT);
                        $kodeBarangAlt = $kategori . $kodeMerk . $kodeJenis . $kodeKemasanAlt;
                        $existsAlt = DB::table('barang')->where('kode_barang', $kodeBarangAlt)->exists();
                        $seq++;
                    } while ($existsAlt && $seq < 100);
                    $kodeKemasan = str_pad((string)($seq-1), 2, '0', STR_PAD_LEFT);
                    $kodeBarang = $kategori . $kodeMerk . $kodeJenis . $kodeKemasan;
                }
                $newBarangId = DB::table('barang')->insertGetId([
                    'kode_barang' => $kodeBarang,
                    'kategori' => $kategori,
                    'merk' => $parent->nama_barang, // nama_barang jadi merk
                    'jenis' => 'Standard', // default jenis
                    'ukuran_kemasan' => $detail->ukuran_kemasan,
                    'harga_barang' => $detail->harga,
                    'stok_barang' => $detail->stok,
                    'deskripsi' => $parent->deskripsi,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update transaksi_items mapping from barang_detail_id -> barang_id
            DB::table('transaksi_items')
                ->where('barang_detail_id', $detail->id)
                ->update(['barang_id' => $newBarangId]);
        }

        // 3) Set barang_id NOT NULL after backfill and drop barang_detail_id
        Schema::table('transaksi_items', function (Blueprint $table) {
            // make barang_id required
            $table->unsignedBigInteger('barang_id')->nullable(false)->change();
        });
        
        // Drop FK and column barang_detail_id safely
        if (Schema::hasColumn('transaksi_items', 'barang_detail_id')) {
            // Try drop foreign key by conventional name
            try { DB::statement('ALTER TABLE `transaksi_items` DROP FOREIGN KEY `transaksi_items_barang_detail_id_foreign`'); } catch (Throwable $e) {}
            // Also try generic dropForeign in case name differs
            try { Schema::table('transaksi_items', function (Blueprint $table) { $table->dropForeign(['barang_detail_id']); }); } catch (Throwable $e) {}
            // Then drop the column
            Schema::table('transaksi_items', function (Blueprint $table) {
                $table->dropColumn('barang_detail_id');
            });
        }

        // Handle legacy table transaksi_old that may reference barang_details
        if (Schema::hasTable('transaksi_old') && Schema::hasColumn('transaksi_old', 'barang_detail_id')) {
            // Drop foreign key constraint to allow dropping barang_details
            try { DB::statement('ALTER TABLE `transaksi_old` DROP FOREIGN KEY `transaksi_barang_detail_id_foreign`'); } catch (Throwable $e) {}
            try { Schema::table('transaksi_old', function (Blueprint $table) { $table->dropForeign(['barang_detail_id']); }); } catch (Throwable $e) {}
            // Keep the column for historical data but without FK
        }

        // 4) Drop barang_details table
        if (Schema::hasTable('barang_details')) {
            Schema::drop('barang_details');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Best-effort partial rollback: recreate barang_details table minimal structure
        if (!Schema::hasTable('barang_details')) {
            Schema::create('barang_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('barang_id');
                $table->string('ukuran_kemasan', 100)->nullable();
                $table->unsignedBigInteger('harga')->default(0);
                $table->integer('stok')->default(0);
                $table->unsignedTinyInteger('index_ukuran')->default(1);
                $table->timestamps();
            });
        }

        // Re-add barang_detail_id to transaksi_items (nullable)
        if (!Schema::hasColumn('transaksi_items', 'barang_detail_id')) {
            Schema::table('transaksi_items', function (Blueprint $table) {
                $table->unsignedBigInteger('barang_detail_id')->nullable()->after('id');
            });
        }

        // Note: we cannot faithfully reconstruct original detail rows
    }
};
