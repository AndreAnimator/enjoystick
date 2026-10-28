<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{

    public function listAll(string $column_name = 'name', string $order = 'asc', ?string $category, ?string $search, bool $is_admin = FALSE): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $perpage = 4;

        $query = Product::with('category');
        if(!$is_admin) {
            $query->where('active', 1);
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($search) {
            $query->whereLike('name', "%$search%", caseSensitive: false);
        }
        $query->orderBy($column_name, $order);

        return $query->paginate($perpage);
    }
    
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function findById(int $id, bool $is_admin = false): Product
    {
        if ($is_admin) {
            return Product::findOrFail($id);    
        }
        return Product::where('active', 1)->findOrFail($id);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);

        return $product;
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }
}