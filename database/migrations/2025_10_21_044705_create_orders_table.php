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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->nullable();
            $table->foreignId('combined_order_id')->constrained('combined_orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
			$table->decimal('vendor_balance')->default(0);
			$table->decimal('admin_balance')->default(0);
            $table->decimal('shipping_charge')->nullable();
            $table->decimal('sub_total');
            $table->decimal('grand_total');
            $table->enum('payment_status', ['paid', 'pending', 'cancelled'])->default("pending");
            $table->enum('order_status', ['pending','process','shipped','delivered','cancel'])->default("pending");
            $table->text('status_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
