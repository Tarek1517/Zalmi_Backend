<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->string('vendor_type');
            $table->string('shopName')->unique();
            $table->string('slug');
            $table->string('image')->nullable();
            $table->string('cvrimage')->nullable();
            $table->string('bio')->nullable();
            $table->string('description')->nullable();
            $table->string('short_description')->nullable();
            $table->enum('type', ['inhouse_shop', 'vendor_shop'])->default('vendor_shop');
            $table->enum('status', ['active', 'inactive', 'deactivated'])->default('inactive');
            $table->integer('order_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
