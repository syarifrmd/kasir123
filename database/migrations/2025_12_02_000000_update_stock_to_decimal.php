<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->decimal('stok_barang', 15, 3)->default(0)->change();
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->decimal('qty_received', 15, 3)->default(0)->change();
            $table->decimal('qty_remaining', 15, 3)->default(0)->change();
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->decimal('qty', 15, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        // Revert back to integer; may lose fractional data
        Schema::table('barang', function (Blueprint $table) {
            $table->integer('stok_barang')->default(0)->change();
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->integer('qty_received')->default(0)->change();
            $table->integer('qty_remaining')->default(0)->change();
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->integer('qty')->default(0)->change();
        });
    }
};
