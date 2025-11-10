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
        Schema::table('barang', function (Blueprint $table) {
            $table->string('kode_merk', 10)->after('kategori')->nullable();
            // Format: 001, 002, 003, etc. Auto-increment per kategori
        });
        
        Schema::table('barang_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('index_ukuran')->after('barang_id')->default(1);
            // Index ukuran: 1, 2, 3, etc. untuk setiap varian ukuran
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('kode_merk');
        });
        
        Schema::table('barang_details', function (Blueprint $table) {
            $table->dropColumn('index_ukuran');
        });
    }
};
