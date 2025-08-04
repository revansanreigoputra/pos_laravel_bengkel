<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->default(0)->after('code_part');
        });
    }

    public function down()
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
        });
    }
};
