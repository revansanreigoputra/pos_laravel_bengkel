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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // stock_alert, purchase, sale, etc.
            $table->morphs('notifiable'); // Untuk relasi ke user/role lain
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->json('data')->nullable(); // Data tambahan (misal: ID sparepart)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
