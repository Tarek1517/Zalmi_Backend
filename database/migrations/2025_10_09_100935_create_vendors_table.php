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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('shopName')->nullable();
            $table->string('vendorName');
            $table->string('licenseNumber');
            $table->string('nid');
            $table->string('email')->unique();
            $table->string('phoneNumber');
            $table->string('password');
            $table->string('documents')->nullable();
            $table->boolean('termsAccepted')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
