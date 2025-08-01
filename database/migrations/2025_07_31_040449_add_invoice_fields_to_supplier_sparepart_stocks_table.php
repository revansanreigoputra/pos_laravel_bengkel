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
        Schema::table('supplier_sparepart_stocks', function (Blueprint $table) {
            // Menambahkan kolom invoice_number
            $table->string('invoice_number')->nullable()->after('received_date'); // Menambahkan kolom invoice_number setelah received_date

            // Menambahkan kolom invoice_file_path
            $table->string('invoice_file_path')->nullable()->after('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_sparepart_stocks', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['invoice_number', 'invoice_file_path']);
        });
    }
};