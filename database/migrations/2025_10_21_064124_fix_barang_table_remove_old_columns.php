<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove old columns: nama_barang, kode_merk, kode_jenis, kode_kemasan
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Check and drop columns if they exist
            if (Schema::hasColumn('barang', 'nama_barang')) {
                $table->dropColumn('nama_barang');
            }
            if (Schema::hasColumn('barang', 'kode_merk')) {
                $table->dropColumn('kode_merk');
            }
            if (Schema::hasColumn('barang', 'kode_jenis')) {
                $table->dropColumn('kode_jenis');
            }
            if (Schema::hasColumn('barang', 'kode_kemasan')) {
                $table->dropColumn('kode_kemasan');
            }
            if (Schema::hasColumn('barang', 'legacy_detail_id')) {
                $table->dropColumn('legacy_detail_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->string('nama_barang')->nullable();
            $table->string('kode_merk', 3)->nullable();
            $table->string('kode_jenis', 2)->nullable();
            $table->string('kode_kemasan', 2)->nullable();
            $table->unsignedBigInteger('legacy_detail_id')->nullable();
        });
    }
};
