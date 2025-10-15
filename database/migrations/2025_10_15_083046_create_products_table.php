<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();

            $table->string('title');
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->default(0);
            $table->integer('stock')->default(1);
            $table->integer('low_stock_threshold')->default(5);
            $table->string('slug');

            $table->boolean('status')->default(1);
            $table->boolean('is_variant')->default(0);
            $table->boolean('featured')->default(0);
            $table->boolean('track_inventory')->default(0);

            $table->string('cover_image')->nullable();
            $table->longText('product_info')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('specification')->nullable();
            $table->json('key_features')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
