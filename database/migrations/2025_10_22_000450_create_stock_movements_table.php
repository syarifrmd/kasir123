<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->enum('type', ['in','out','adjust']);
            $table->integer('qty');
            $table->integer('before_stock')->nullable();
            $table->integer('after_stock')->nullable();
            $table->decimal('unit_cost', 12,2)->nullable(); // hpp per unit terkait movement
            $table->decimal('unit_price', 12,2)->nullable(); // harga jual saat itu (jika relevan)
            $table->unsignedBigInteger('stock_batch_id')->nullable();
            $table->unsignedBigInteger('transaksi_item_id')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('stock_batch_id')->references('id')->on('stock_batches')->nullOnDelete();
            $table->foreign('transaksi_item_id')->references('id')->on('transaksi_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
