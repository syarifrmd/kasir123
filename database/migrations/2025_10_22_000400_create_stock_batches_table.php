<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->integer('qty_received');
            $table->integer('qty_remaining');
            $table->decimal('unit_cost', 12, 2); // HPP per unit saat diterima
            $table->decimal('sell_price_at_receive', 12, 2)->nullable(); // harga jual saat batch masuk
            $table->timestamp('received_at')->useCurrent();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
