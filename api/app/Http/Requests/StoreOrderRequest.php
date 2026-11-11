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
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user()->id,
        ]);
    }
}
