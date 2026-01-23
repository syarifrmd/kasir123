<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop Old Tables
        // We disable foreign key checks to ensure we can drop tables in any order
        Schema::disableForeignKeyConstraints();

        // List of tables to drop based on the "old" schema description
        $tablesToDrop = [
            'transaksi_items',
            'transaksi_detail', // Just in case
            'transaksi',
            'transaksi_old', // From previous backup
            'stock_movements',
            'stock_batches',
            'barang_details',
            'vendors',
            // We also interpret "barang" as needing a refresh to match the new simplified structure exactly
            'barang',
            // 'kontak' and 'transaksi_ledger' might exist if re-running, so drop them too
            'kontak',
            'transaksi_ledger',
        ];

        foreach ($tablesToDrop as $table) {
            Schema::dropIfExists($table);
        }

        // 2. Create 'kontak' table (Unified Vendor & Customer)
        Schema::create('kontak', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('tipe', ['VENDOR', 'CUSTOMER']);
            $table->string('hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        // 3. Create 'barang' table (Master Product)
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang');
            $table->string('kategori')->default('General');
            $table->string('satuan', 20)->default('pcs'); // e.g., 'kg', 'pcs'
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('stok', 15, 3)->default(0); // Supports 'eceran'
            $table->timestamps();
        });

        // 4. Update 'users' table to have 'role'
        // Check if column exists, if not add it.
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'kasir'])->default('kasir')->after('email');
            });
        }

        // 5. Create 'transaksi_ledger' table
        Schema::create('transaksi_ledger', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->index(); // Invoice ID
            $table->dateTime('tanggal');
            $table->enum('jenis_transaksi', ['MASUK', 'KELUAR']);
            
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->foreignId('kontak_id')->nullable()->constrained('kontak')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users'); // Assumes users table exists
            
            $table->decimal('qty', 15, 3);
            $table->decimal('harga_realisasi', 15, 2); // Buy Price (MASUK) or Sell Price (KELUAR)
            
            $table->date('expired_date')->nullable();
            $table->enum('metode_bayar', ['CASH', 'QRIS', 'TRANSFER'])->default('CASH');
            $table->enum('status', ['LUNAS', 'HUTANG'])->default('LUNAS');
            
            $table->timestamps();
        });

        // 6. Create Database Trigger for Stock Management
        // We use DB::unprepared for raw SQL statements
        
        // Drop trigger if exists (for idempotency)
        DB::unprepared('DROP TRIGGER IF EXISTS tr_stock_update_after_insert');

        DB::unprepared("
            CREATE TRIGGER tr_stock_update_after_insert
            AFTER INSERT ON transaksi_ledger
            FOR EACH ROW
            BEGIN
                IF NEW.jenis_transaksi = 'MASUK' THEN
                    UPDATE barang 
                    SET stok = stok + NEW.qty,
                        updated_at = NOW()
                    WHERE id = NEW.barang_id;
                ELSEIF NEW.jenis_transaksi = 'KELUAR' THEN
                    UPDATE barang 
                    SET stok = stok - NEW.qty,
                        updated_at = NOW()
                    WHERE id = NEW.barang_id;
                END IF;
            END
        ");

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        
        DB::unprepared('DROP TRIGGER IF EXISTS tr_stock_update_after_insert');
        
        Schema::dropIfExists('transaksi_ledger');
        Schema::dropIfExists('barang');
        Schema::dropIfExists('kontak');
        
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
        
        // Note: We do not recreate the old complex tables in down() 
        // because it's a destructive refactor.
        
        Schema::enableForeignKeyConstraints();
    }
};
