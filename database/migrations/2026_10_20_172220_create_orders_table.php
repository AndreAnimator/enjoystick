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
            $table->foreignId('user_id')->unique()->deleteOnCascade();
            $table->foreignId('address_id')->restrictOn;
            $table->decimal('total', 10, 2);
            $table->enum('status', ['waiting', 'paid', 'sent', 'arrived', 'canceled'])->default('waiting');
            $table->dateTime('sent_at', precision: 0)->nullable();
            $table->dateTime('arrived_at', precision: 0)->nullable();
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
