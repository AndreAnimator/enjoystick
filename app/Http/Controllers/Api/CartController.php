<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateCartItemRequest;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = null;
        $token = $request->header('X-Cart-Token') ?? null;
        $cart = CartResource($this->cartService->resolve($user, $token));
        return $this->cartService->summary($cart);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
