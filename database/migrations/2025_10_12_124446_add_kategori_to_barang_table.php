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
            $table->string('kategori', 2)->after('kode_barang')->default('SK');
            // Kategori: RK=Rokok, MN=Minuman, SB=Sembako, SK=Snack, OB=Obat, EK=Elektronik
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
