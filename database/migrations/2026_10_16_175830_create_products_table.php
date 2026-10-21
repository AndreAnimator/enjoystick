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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete(); // TODO impedir de deletar categorias com produtos
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->unsigned();
            $table->string('image', 255)->nullable();
            $table->integer('stock')->default('0')->unsigned();
            $table->tinyInteger('active')->default('1');
            $table->index('products');
            $table->index('category_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
