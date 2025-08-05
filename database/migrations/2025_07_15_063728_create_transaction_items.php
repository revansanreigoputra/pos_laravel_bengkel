<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->enum('item_type', ['service', 'sparepart']);
            $table->unsignedBigInteger('item_id'); // id dari service/sparepart

            // Tambahkan kolom untuk melacak batch pembelian
            $table->foreignId('purchase_order_item_id')->nullable()->constrained()->onDelete('restrict');

            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
