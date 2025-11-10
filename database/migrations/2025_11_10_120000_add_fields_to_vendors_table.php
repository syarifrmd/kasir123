<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('kode', 20)->nullable()->unique()->after('id');
            $table->string('no_kontak', 30)->nullable()->after('telepon');
            $table->string('nama_sales', 100)->nullable()->after('no_kontak');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropColumn(['kode','no_kontak','nama_sales']);
        });
    }
};
