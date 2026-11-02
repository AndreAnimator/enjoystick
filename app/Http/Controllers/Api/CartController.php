<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use Illuminate\Http\Request;
use App\Http\Resources\CartResource;
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
        $cart = $this->cartService->resolve($user, $token);
        // CartResource é inútil aqui?
        return $this->cartService->summary($cart);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartItemRequest $request)
    {
        // TODO perguntar se faço outra função pra isso
        $user = null;
        $token = $request->header('X-Cart-Token') ?? null;
        $cart = $this->cartService->resolve($user, $token);

        $item = $request->validated();
        return $this->cartService->addItem($cart, $item['product_id'], $item['quantity']);
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
