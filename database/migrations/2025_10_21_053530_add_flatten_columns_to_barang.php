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
        Schema::table('barang', function (Blueprint $table) {
            // New flattened fields
            $table->string('kode_jenis', 2)->nullable()->after('kode_merk'); // 2 digits
            $table->string('kode_kemasan', 2)->nullable()->after('kode_jenis'); // 2 digits
            $table->string('ukuran_kemasan', 100)->nullable()->after('kode_kemasan');
            $table->unsignedBigInteger('legacy_detail_id')->nullable()->after('deskripsi');

            // 9-digit code stored in kode_barang without dashes
            // Keep kode_barang column; we'll recompute values in data migration
        });

        // Adjust column types only when supported (avoid sqlite change() issues)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('barang', function (Blueprint $table) {
                $table->unsignedBigInteger('harga_barang')->default(0)->change(); // ensure integer prices
                $table->integer('stok_barang')->default(0)->change(); // ensure integer stocks
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn(['kode_jenis', 'kode_kemasan', 'ukuran_kemasan', 'legacy_detail_id']);
            // Can't easily revert type changes for harga_barang, stok_barang here safely
        });
    }
};
