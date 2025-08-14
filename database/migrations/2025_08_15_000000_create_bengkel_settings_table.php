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
        Schema::create('bengkel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bengkel')->default('BengkelKu');
            $table->text('alamat_bengkel')->nullable();
            $table->string('telepon_bengkel')->nullable();
            $table->string('email_bengkel')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bengkel_settings');
    }
};