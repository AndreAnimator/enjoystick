<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->id,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shipping_cost,
            'total' => $this->total,
            'shipping_recipient_name' => $this->shipping_recipient_name,
            'shipping_postal_code' => $this->shipping_postal_code,
            'shipping_street' => $this->shipping_street,
            'shipping_number' => $this->shipping_number,
            'shipping_complement' => $this->shipping_complement,
            'shipping_neighborhood' => $this->shipping_neighborhood,
            'shipping_city' => $this->shipping_city,
            'shipping_state' => $this->shipping_state,
            'shipping_country' => $this->shipping_country,
            'paid_at' => $this->paid_at,
            'sent_at' => $this->sent_at,
            'arrived_at' => $this->arrived_at,
            'cancelled_at' => $this->cancelled_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
