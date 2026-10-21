<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'image',
        'stock',
        'active',
    ];

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class)->withPivot('quantity');
    }

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->withPivot('product_name', 'unit_price', 'quantity');
    }
}
