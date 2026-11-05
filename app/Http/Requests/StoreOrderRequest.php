<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request. TODO: change all authroize and finish this request and finish OrderController
     */
    public function authorize(): bool
    {
        //  return $this->user()->id == $this->user_id;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /*
            $table->foreignId('user_id')->unique()->restrictOnDelete();
            $table->enum('status', ['waiting', 'paid', 'sent', 'arrived', 'canceled'])->default('waiting');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->string('shipping_recipient_name', 100);
            $table->string('shipping_postal_code', 20);
            $table->string('shipping_street', 150);
            $table->string('shipping_number', 20);
            $table->string('shipping_complement', 100)->nullable();
            $table->string('shipping_neighborhood', 100);
            $table->string('shipping_city', 100);
            $table->string('shipping_state', 100);
            $table->string('shipping_country', 100);
            $table->dateTime('paid_at', precision: 0)->nullable();
            $table->dateTime('sent_at', precision: 0)->nullable();
            $table->dateTime('arrived_at', precision: 0)->nullable();
            $table->dateTime('cancelled_at', precision: 0)->nullable();
            $table->index('status');
            $table->timestamps(); 
            */
        ];
    }
}
