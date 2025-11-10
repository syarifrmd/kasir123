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
        Schema::create('barang_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->string('kode_detail', 50)->unique();
            $table->string('ukuran_kemasan', 100); // e.g., "1 Liter", "Box 12 pcs", "Sachet 10g"
            $table->decimal('harga', 15, 2)->default(0);
            $table->unsignedInteger('stok')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_details');
    }
};
