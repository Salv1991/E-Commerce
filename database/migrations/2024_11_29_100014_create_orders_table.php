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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->integer('discount')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('payment_fee', 10, 2)->default(0);
            $table->string('shipping_method')->nullable()->default('elta');
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->json('adress')->nullable();
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
