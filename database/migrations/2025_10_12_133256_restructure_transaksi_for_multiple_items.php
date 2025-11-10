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
        // Rename transaksi to transaksi_items (backup old structure)
        Schema::rename('transaksi', 'transaksi_old');
        
        // Create new transaksi table (header/master)
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 30)->unique();
            $table->date('tanggal_transaksi');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->decimal('bayar_tunai', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->enum('metode_transaksi', ['qris', 'transfer_bank', 'cash']);
            $table->enum('status_transaksi', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->string('kasir', 100)->nullable();
            $table->timestamps();
        });
        
        // Create transaksi_items table (detail items)
        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('barang_detail_id')->constrained('barang_details')->onDelete('restrict');
            $table->unsignedInteger('qty');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_items');
        Schema::dropIfExists('transaksi');
        Schema::rename('transaksi_old', 'transaksi');
    }
};
