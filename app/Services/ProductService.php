<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquente\Collection;

class ProductService
{

    public function listAll(string $column_name = 'name', string $order = 'asc', string $category): Collection
    {
        $perpage = 4;
        $query = Product::with('category')->where('active', 1)->paginate($perpage)->orderBy($column_name, $order);
        if ($category)
            return $query->where('category', $category)->get();
        return $query->get();
    }

    public function listAllAdmin(string $column_name = 'name', string $order = 'asc', string $category): Collection
    {
        $perpage = 4;
        $query = Product::with('category')->all()->paginate($perpage)->orderBy($column_name, $order);
        if ($category)
            return $query->where('category', $category)->get();
        return $query->get();
    }

    public function search(string $column_name = 'name', string $order = 'asc', string $category, string $search): Collection
    {
        $perpage = 10;
        $query = Product::with('category')->where($active, 1)->whereLike('name', $search, caseSensitive: false)->paginate($perpage)->orderBy($column_name, $order);
        if ($category)
            return $query->where('category', $category)->get();
        return $query->get();
    }

    public function searchAdmin(string $column_name = 'name', string $order = 'asc', string $category, string $search): Collection
    {
        $perpage = 10;
        $query = Product::whereLike('name', $search, caseSensitive: false)->paginate($perpage)->orderBy($column_name, $order);
        if ($category)
            return $query->where('category', $category)->get();
        return $query->get();
        
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function findById(int $id): Product
    {
        return Product::findOrFail($id)->where('active', 1);
    }

    public function findByIdAdmin(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function update(int $id, array $data): Product
    {
        Product::findOrFail($id)->delete();
    }
}