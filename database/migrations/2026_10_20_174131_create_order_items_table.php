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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->cascadeOnDelete();
            $table->foreignId('product_id')->restrictOnDelete();
            $table->primary(['order_id', 'product_id'])->unique();
            $table->string('product_name', 255);
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->unsigned();
            $table->timestamps();
        });
    }
    /*
    CONSTRAINT chk_order_items_qty CHECK (quantity > 0)
    */

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
