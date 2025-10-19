<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Step 1: Add the column
            $table->unsignedBigInteger('shop_id')
                  ->nullable()
                  ->after('brand_id'); // Optional

            // Step 2: Add the foreign key constraint
            $table->foreign('shop_id')
                  ->references('id')
                  ->on('shops')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });
    }
};


