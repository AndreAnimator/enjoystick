<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquente\Collection;

class ProductService
{
    public function listAllAdmin(): Collection
    {
        return Product::with('category')->all();
    }

    public function listAll(): Collection
    {
        return Product::with('category')->where('active', 1);
    }

    public function search(string $search): Collection
    {
        return Product::with('category')->where($active)->whereLike('name', $search, caseSensitive: false)->get();
    }

    public function searchAdmin(): Collection
    {
        return Product::whereLike('name', $search, caseSensitive: false)->get();
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