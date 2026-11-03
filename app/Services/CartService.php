<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquente\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CartService
{
    private function generateToken() {
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
            return Cart::create(['token' => $this->generateToken(), 'user_id' => null]);
        }

        $cart = Cart::where('token', $token)->first();

        if (!$cart) {
            return Cart::create(['token' => $this->generateToken(), 'user_id' => null]);
        }

        if ($cart->user_id !== null) {
            return Cart::create(['token' => $this->generateToken(), 'user_id' => null]);
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
            return Cart::create(['user_id' => $user->id, 'token' => $this->generateToken()]);
        } catch (UniqueConstraintViolation) {
            Cart::where('user_id', $user->id)->firstOrFail();
        }
    }

    public function resolve(?User $user, ?string $token)
    {
        return $user ? $this->findOrCreateForUser($user) : $this->findOrCreateForVisitor($token);
    }

    public function addItem(Cart $cart, int $productId, int $quantity): CartItem
    {
        /*
        if ($quantity < 1) {
            ValidationException (422); acho que isso vai ser tratado no controller com requests
        }
        */
        return DB::transaction(function () use ($cart, $productId, $quantity) {
            $product = Product::lockForUpdate()->findOrFail($productId);
            if (!$product->active) {
                // 422 "produto indisponível"
                throw ValidationException::withMessages([
                    'message' => 'Produto indisponível.'
                ]);
            }
            if ($product->stock < 1) {
                // 422 "sem estoque"
                throw ValidationException::withMessages([
                    'message' => 'Produto sem estoque.'
                ]);
            }

            $item = $cart->items()->where('product_id', $productId)->first();

            if ($item) {
                $newQuantity = min($item->quantity + $quantity, $product->stock);
                if ($newQuantity === $item->quantity) {
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
            // TODO retorno com o produto junto?
            return $item;
        });
    }

    public function updateItemQuantity(Cart $cart, int $itemId, int $itemQuantity): void
    {
        DB::transaction(function () use ($cart, $itemQuantity, $itemId) {
            $item = $cart->items()->lockForUpdate()->findOrFail($itemId);
            if ($itemQuantity < 1) {
                $item->delete();
                return;
            }
            $product = Product::lockForUpdate()->findOrFail($item->product_id);
            if (!$product->active) {
                throw ValidationException::withMessages([
                    'message' => 'Produto inativo.'
                ]);
            }
            if ($product->stock < 1) {
                throw ValidationException::withMessages([
                    'message' => 'Produto sem estoque.'
                ]);
            }
            $newQuantity = min($itemQuantity, $product->stock);
            $item->quantity = $newQuantity;
            $item->save();
        });
    }

    public function removeItem(Cart $cart, int $itemId): void
    {
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();
    }

    public function summary(Cart $cart): array
    {
        $items = $cart->items()->with('product')->get();
        $token = $cart->token;
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
            $lines[] = [$product->id, $product->name, $product->price, $item->quantity, $subtotal, $available];
        }
        return [
            'items' => $lines,
            'token' => $token,
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
        if ($visitorCart->user_id !== null) {
            throw ValidationException::withMessages([
                'message' => 'Não é carrinho de visitante.'
            ]);
        }

        $warnings = [];

        return DB::transaction(function () use ($warnings, $user, $visitorCart) {
            $userCart = Cart::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$userCart) {
                $userCart == Cart::create(['user_id' => $user->id, 'token' => $this->generateToken()]);
            }

            $visitorItems = $visitorCart->items()->with('product')->get();

            foreach ($visitorItems as $visitorItem) {
                $product = Product::lockForUpdate()->find($visitorItem->product_id);
                if (!$product || $product->active || $product->stock < 1) {
                    $warnings[] = "$product->name está indisponível, foi descartado.";
                    continue;
                }
                $exists = $userCart->items()->where('product_id', $visitorItem->product_id)->first();
                if ($exists) {
                    $wanting = $exists->quantity + $visitorItem->quantity;
                    $newQuantity = min($wanting, $product->stock);
                    if ($newQuantity < $quantity) {
                        $warnings[] = "Quantidade de $product->name limitado ao estoque, $newQuantity";
                    }
                    $exists->quantity = $newQuantity;
                    $exists->save();
                }
                else {
                    $newQuantity = min($visitorItem->quantity, $product->stock);
                    $userCart->items()->create([
                        'product_id' => $visitorItem->product_id,
                        'quantity' => $newQuantity,
                    ]);
                }
            }
            $this->deleteCart($visitorCart);
            return ['cart' => $userCart, 'warnings' => $warnings];
        });
    }

    public function clearItems(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function deleteCart(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->delete();
    }
}