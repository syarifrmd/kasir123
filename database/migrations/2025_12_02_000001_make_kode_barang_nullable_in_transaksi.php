<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NOTE: 'kode_barang' column does not exist in 'transaksi' table in the current schema.
        // It was removed/restructured in previous migrations.
        // Ignoring this operation to prevent migration errors.
        
        /*
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('kode_barang', 30)->nullable()->change();
        });
        */
    }

    public function down(): void
    {
        /*
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('kode_barang', 30)->nullable(false)->change();
        });
        */
    }
};
