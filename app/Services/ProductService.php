<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquente\Collection;

class ProductService
{
    public function listAll(): Collection
    {
        return Product::all();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function findById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function update(int $id, array $data): Product
    {
        Product::findOrFail($id)->delete();
    }
}