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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->cascadeOnDelete();
            $table->foreignId('product_id')->restrictOnDelete();
            $table->unique(['cart_id', 'product_id']);
            $table->integer('quantity')->unsigned()->default(1);
            $table->timestamps();
        });
    }

    /*
    UNIQUE KEY uq_cart_product (cart_id, product_id),
    CONSTRAINT chk_cart_items_qty CHECK (quantity > 0),
    */

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
