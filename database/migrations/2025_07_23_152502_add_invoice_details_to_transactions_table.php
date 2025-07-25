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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('id'); // Temporarily nullable
            $table->string('vehicle_model')->nullable()->after('vehicle_number');
            $table->string('payment_method')->after('total_price');
            $table->string('proof_of_transfer_url')->nullable()->after('payment_method');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending')->after('proof_of_transfer_url');
        });

        DB::table('transactions')->get()->each(function ($transaction) {
            // Hanya perbarui jika invoice_number kosong (null atau string kosong)
            if (empty($transaction->invoice_number)) {
                DB::table('transactions')
                  ->where('id', $transaction->id)
                  ->update(['invoice_number' => 'OLD-INV-' . $transaction->id . '-' . uniqid()]);
            }
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('invoice_number')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number',
                'vehicle_model',
                'payment_method',
                'proof_of_transfer_url',
                'status'
            ]);
        });
    }
};