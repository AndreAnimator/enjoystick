<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquente\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function generateToken() {
        return Str::random(64);
    }

    public function getByToken(?string $token): Cart
    {
        if (!$token) {
            $token = generateToken();
            $data = ['token' => $token];
            create($data);
        }
        return Cart::where($token);
    }

    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    public function findById(int $id): Cart
    {
        return Cart::findOrFail($id);
    }

    public function findOrCreateForVisitor(?string $token): Cart
    {
        if (!$token) {
            return Cart::create(['token' => generateToken(), 'user_id' => null]);
        }

        $cart = Cart::where('token', $token)->first();

        if (!$cart) {
            return Cart::create(['token' => generateToken(), 'user_id' => null]);
        }

        if ($cart.user_id !== null) {
            return Cart::create(['token' => generateToken(), 'user_id' => null]);
        }

        return $cart;
    }

    public function findOrCreateForUser(User $user): Cart
    {
        $cart = Cart::where('user_id', $user->id)->first();
        if ($cart) {
            return $cart;
        }
        try{
            return Cart::create(['user_id' => $user->id, 'token' => generateToken()]);
        } catch (UniqueConstraintViolation) {
            Cart::where('user_id', $user->id)->firstOrFail();
        }
    }

    public function resolve(?User $user, ?string $token)
    {
        return $user ? findOrCreateForUser($user) : findOrCreateForVisitor;
    }

    public function addItem(Cart $cart, int $productId, int $quantity): CartItem
    {
        /*
        if ($quantity < 1) {
            ValidationException (422); acho que isso vai ser tratado no controller com requests
        }
        */
        return DB::transaction(function () {
            $product = Product::lockForUpdate()->findOrFail($productId);
            if (!$product->active) {
                // 422 "produto indisponível"
                return response()->json([
                    'message' => 'Produto indisponível.'
                ], 422);
            }
            if ($product->stock < 1) {
                // 422 "sem estoque"
                return response()->json([
                    'message' => 'Produto sem estoque.'
                ], 422);
            }

            $item = $cart->items()->where('product_id', $productId)->first();

            if ($item) {
                $newQuantity = min($item->quantity + $quantity, $product->stock);
                if ($newQuantity === $itemQuantity) {
                    return $item;
                }
                $item->quantity = $newQuantity;
                $item->save();
            }
            else {
                $finalQuantity = min($quantity, $product->stock);
                $item = $cart->items()->create([
                    'product_id' => $productId,
                    'quantity'   => $finalQuantity,
                ]);
            }
            return $item;
        });
    }

    public function updateItemQuantity(Cart $cart, int $itemId, int $itemQuantity): void
    {
        $item = $cart->$items()->findOrFail($itemId);
        if ($quantity < 1) {
            $item->delete();
            return;
        }
        DB::transaction(function () {
            $product = Product::lockForUpdate()->findOrFail($item->product_id);
            if (!$product->active) {
                return response()->json([
                    'message' => 'Produto inativo.'
                ], 422);
            }
            if ($product->stock < 1) {
                return response()->json([
                    'message' => 'Produto sem estoque.'
                ], 422);
            }
            $newQuantity = min($quantity, $product->stock);
            $item->quantity = $newQuantity;
            $item->save();
        });
        return;
    }

    public function removeItem(Cart $cart, int $itemId): void
    {
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();
    }

    public function summary(Cart $cart): array
    {
        $items = $cart->items()->with('product')->get();

        $lines = [];
        $total = 0;
        $hasUnavailable = false;
        foreach($items as $item) {
            $product = $item->product;
            $available = $product && $product->active && $product->stock > 0;
            if (!$available) {
                $hasUnavailable = true;
            }
            $subtotal = $available ? $product->price * $item->quantity : 0;
            $total += $subtotal;
            $lines[] = [$product->id, $product->name, $product->price, $subtotal, $available];
        }
        return [
            'items' => $lines,
            'total' => $total,
            'hasUnavailableItems' => $hasUnavailable,
        ];
    }

    public function update(int $id, array $data): Cart
    {
        $cart = Cart::findOrFail($id);
        $cart->update($data);
        return $cart;
    }

    public function delete(int $id): void
    {
        Cart::findOrFail($id)->delete();
    }

    public function mergeVisitorIntoUser(Cart $visitorCart, User $user): array
    {

    }

    public function clearItems(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function deleteCart(int $id): void
    {
        Cart::findOrFail($id)->delete();
    }
}