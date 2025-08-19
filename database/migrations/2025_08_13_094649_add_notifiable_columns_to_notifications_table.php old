<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // tambahkan morphs notifiable (2 kolom: notifiable_type, notifiable_id)
            $table->string('notifiable_type')->after('type');
            $table->unsignedBigInteger('notifiable_id')->after('notifiable_type');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notifiable_type', 'notifiable_id']);
        });
    }
};