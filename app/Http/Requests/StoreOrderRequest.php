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
            'user_id' => ['required', 'exists:users,id'],
            'shipping_recipient_name' => ['required', 'string',  'max:100'],
            'shipping_postal_code' => ['required', 'string',  'max:20'],
            'shipping_street' => ['required', 'string',  'max:150'],
            'shipping_number' => ['required', 'string',  'max:20'],
            'shipping_complement' => ['nullable', 'string',  'max:100'],
            'shipping_neighborhood' => ['required', 'string',  'max:100'],
            'shipping_city' => ['required', 'string',  'max:100'],
            'shipping_state' => ['required', 'string',  'max:100'],
            'shipping_country' => ['required', 'string',  'max:100'],
            /*
            $table->foreignId('user_id')->unique()->restrictOnDelete();
            $table->enum('status', ['waiting', 'paid', 'sent', 'arrived', 'canceled'])->default('waiting');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->dateTime('paid_at', precision: 0)->nullable();
            $table->dateTime('sent_at', precision: 0)->nullable();
            $table->dateTime('arrived_at', precision: 0)->nullable();
            $table->dateTime('cancelled_at', precision: 0)->nullable();
            $table->index('status');
            $table->timestamps(); 
            */
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user()->id,
        ]);
    }
}
