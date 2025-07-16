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
        Schema::create('jenis_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Contoh: mobil matic, motor matic, motor matic, motor manual, mobil matic, mobil manual
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_kendaraans');
    }
};
