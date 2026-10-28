<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //string $column_name = 'name', string $order = 'asc', string $category
        $data = $request->validate([
            'column'   => 'sometimes|in:name,price,created_at',
            'order'    => 'sometimes|in:asc,desc',
            'category' => 'sometimes|integer|exists:categories,id',
            'q' => 'sometimes',
        ]);
        $column = $data['column'] ?? 'name';
        $order = $data['order'] ?? 'asc';
        $category = $data['category'] ?? null;
        $search = $data['q'] ?? null;
        $is_admin = false;
        return ProductResource::collection($this->productService->listAll($column, $order, $category, $search, $is_admin    ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());
        
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $is_admin = false;
        return new ProductResource($this->productService->findById($id, $is_admin));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        $product = $this->productService->update($id, $request->validated());
    
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->productService->delete($id);

        return response()->noContent();
    }
}
