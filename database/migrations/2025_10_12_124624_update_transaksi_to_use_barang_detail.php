<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists
        if (!Schema::hasColumn('transaksi', 'barang_detail_id')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->unsignedBigInteger('barang_detail_id')->after('kode_transaksi')->nullable();
            });
        }
        
        // Clear existing transactions for clean migration
        DB::table('transaksi')->truncate();
        
        // Drop old foreign key if exists
        if (Schema::hasColumn('transaksi', 'kode_barang')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropForeign(['kode_barang']);
                $table->dropColumn('kode_barang');
            });
        }
        
        // Make barang_detail_id required and add foreign key
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_detail_id')->nullable(false)->change();
            $table->foreign('barang_detail_id')->references('id')->on('barang_details')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['barang_detail_id']);
            $table->dropColumn('barang_detail_id');
            
            $table->string('kode_barang', 30)->after('kode_transaksi');
            $table->foreign('kode_barang')->references('kode_barang')->on('barang')->cascadeOnUpdate()->restrictOnDelete();
        });
    }
};
