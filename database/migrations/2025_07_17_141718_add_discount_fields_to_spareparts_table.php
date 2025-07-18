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
        Schema::table('spareparts', function (Blueprint $table) {
            // Menambahkan kolom setelah 'selling_price' agar rapi
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('selling_price');
            $table->date('discount_start_date')->nullable()->after('discount_percentage');
            $table->date('discount_end_date')->nullable()->after('discount_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'discount_start_date', 'discount_end_date']);
        });
    }
};