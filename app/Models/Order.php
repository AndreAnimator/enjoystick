<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'subtotal',
        'shipping_cost',
        'total',
        'shipping_recipient_name',
        'shipping_postal_code',
        'shipping_street',
        'shipping_number',
        'shipping_complement',
        'shipping_neighborhood',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'paid_at',
        'sent_at',
        'arrived_at',
        'cancelled_at',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): HasMany
    {
        return $this->haMany(Product::class)->withPivot('product_name', 'unit_price', 'quantity');
    }
}
