<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaksi_items', function (Blueprint $table) {
            $table->decimal('hpp', 12, 2)->default(0)->after('subtotal'); // total cost (COGS) untuk item ini
            $table->decimal('profit', 12, 2)->default(0)->after('hpp');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_items', function (Blueprint $table) {
            $table->dropColumn(['hpp','profit']);
        });
    }
};
