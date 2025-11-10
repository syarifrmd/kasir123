<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 30)->unique();
            $table->string('kode_barang', 30); // reference to barang.kode_barang
            $table->unsignedInteger('volume_barang');
            $table->date('tanggal_transaksi');
            $table->enum('metode_transaksi', ['qris', 'transfer_bank', 'cash']);
            $table->enum('status_transaksi', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->timestamps();

            $table->foreign('kode_barang')->references('kode_barang')->on('barang')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
