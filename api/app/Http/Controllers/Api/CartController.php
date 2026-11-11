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
use App\Models\Cart;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}
    /**
     * Display a listing of the resource.
     */

    public function getCart(Request $request): Cart
    {
        $user = $request->user() ?? null;
        $token = $request->header('X-Cart-Token') ?? null;
        $cart = $this->cartService->resolve($user, $token);
        return $cart;
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        return $this->cartService->summary($cart);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartItemRequest $request)
    {
        $cart = $this->getCart($request);

        $data = $request->validated();
        $item = $this->cartService->addItem($cart, $data['product_id'], $data['quantity']);
        return response()->json(array_merge($item->toArray(), ['token' => $cart->token]));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartItemRequest $request, int $id)
    {
        $cart = $this->getCart($request);
        $array = $request->validated();
        $item = $this->cartService->updateItemQuantity($cart, $id, $array['quantity']);
        return $item;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $cart = $this->getCart($request);
        $this->cartService->removeItem($cart, $id);
        return response()->noContent();
    }
}
