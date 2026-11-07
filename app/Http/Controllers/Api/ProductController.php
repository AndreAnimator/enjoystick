<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Http\Library\ApiHelpers;
use Illuminate\Auth\Access\AuthorizationException;

class ProductController extends Controller
{
    use ApiHelpers;

    public function __construct(
        private ProductService $productService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'column'   => 'sometimes|in:name,price,created_at',
            'order'    => 'sometimes|in:asc,desc',
            'category' => 'sometimes|integer|nullable|exists:categories,id',
            'q' => 'sometimes',
        ]);
        $column = $data['column'] ?? 'name';
        $order = $data['order'] ?? 'asc';
        $category = $data['category'] ?? null;
        $search = $data['q'] ?? null;
        $is_admin = $this->isAdmin($request->user());
        return ProductResource::collection($this->productService->listAll($column, $order, $category, $search, $is_admin));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $is_admin = $this->isAdmin($request->user());
        if (!$is_admin) {
            throw new AuthorizationException('This action is unauthorized.');
        }
        $product = $this->productService->create($request->validated());
        
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        $is_admin = $this->isAdmin($request->user());
        return new ProductResource($this->productService->findById($id, $is_admin));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        $is_admin = $this->isAdmin($request->user());
        if (!$is_admin) {
            throw new AuthorizationException('This action is unauthorized.');
        }
        $product = $this->productService->update($id, $request->validated());
    
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $is_admin = $this->isAdmin($request->user());
        if (!$is_admin) {
            throw new AuthorizationException('This action is unauthorized.');
        }
        $this->productService->delete($id);

        return response()->noContent();
    }
}
