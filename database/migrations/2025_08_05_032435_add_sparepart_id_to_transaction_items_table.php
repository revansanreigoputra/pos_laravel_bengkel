<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            // Tambahkan kolom baru setelah kolom 'transaction_id'
            $table->foreignId('sparepart_id')->nullable()->constrained()->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropForeign(['sparepart_id']);
            $table->dropColumn('sparepart_id');
        });
    }
};