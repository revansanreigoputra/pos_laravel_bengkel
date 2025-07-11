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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('nama_barang')->nullable();
            $table->enum('tipe_barang', ['satuan', 'set'])->default('satuan');
            $table->integer('jumlah')->default(0);
            $table->decimal('harga', 15, 2)->default(0); // harga dengan 2 angka desimal
            $table->date('tanggal_masuk')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
