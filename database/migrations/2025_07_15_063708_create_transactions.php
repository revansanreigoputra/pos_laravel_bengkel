<?php
// database/migrations/xxxx_xx_xx_create_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict'); // Menambahkan foreign key ke tabel customers
            $table->string('vehicle_number')->nullable(); 
            $table->string('vehicle_model')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->decimal('total_price', 12, 2)->default(0);
            // $table->decimal('discount_amount', 12, 2)->default(0)->nullable(); // Menambahkan nullable
            $table->string('invoice_number')->unique()->nullable();
            $table->string('payment_method')->nullable();
            $table->string('proof_of_transfer_url')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};