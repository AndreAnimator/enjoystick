<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquente\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(array $data, Request $request): Order
    {
        return DB::transaction(function () use ($data, $request) {
            $order = Order::create($data);
            $order->user_id = $request->user()->id;
            $oder->save();
            // Dava pra pegar por token também
            $cart = Cart::where('user_id', $request->user()->id)->lockForUpdate()->first();
            if (!$userCart || $cart->items() === null) {
                throw ValidationException::withMessages([
                    'message' => 'Pedido feito com carrinho vazio/inexiste.'
                ]);
            }
            $cartItems = $cart->items()->with('product')->get();
            $subtotal = 0.00;
            $total = 0.00;
            $shippingCost = 10.00;
            
            foreach ($cartItems as $cartItem) {
                $product = Product::lockForUpdate()->find($cartItem->product_id);
                if ($cartItem->quantity < 1) {
                    $item->delete();
                    throw ValidationException::withMessages([
                        'message' => 'Quantidade de item do carrinho inválido.'
                    ]);
                }
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
                $quantity = $product->quantity - $cartItem->quantity;
                $quantity = ($quantity < 0) ? 0 : $quantity;
                $newQuantity = min( $product->quantity, $cartItem->quantity);
                $product->quantity = $quantity;
                $subtotal += $newQuantity;
                $product->save();
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product()->name,
                    'unit_price' => $cartItem->product()->price,
                    'quantity' => $newQuantity,
                ]);
            }
            $total = $subtotal + $shippingCost;
            $cart->total = $total;
            $cart->subtotal = $subtotal;
            $cart->shipping_cost = $shippingCost;
            $cart->items()->delete();
            $cart->delete();
            return $order;
        });
    }

    public function listUserOrders(Request $request): Collection
    {
        return Order::where('user_id', $request->user()->id);  
    }

    public function listAll(): Collection
    {
        return Order::all();  
    }

    public function findById(Request $request, int $id): ?Order
    {
        $order = Order::findOrFail($id);
        if ($request->user()->id !== $order->user_id) {
            throw new AuthorizationException('This is not your order.');
            return null;
        }
        return $order;
    }

    public function pay(Request $request, int $id): Order
    {
        $order = Order::findOrFail($id);
        if ($request->user()->id !== $order->user_id) {
            throw new AuthorizationException('This is not your order.');
        }
        $order->status = "paid";
        $order->paid_at = now();
        $order->save();
        return $order;
    }

    public function update(string $status, int $id): Order
    {
        $order = Order::findOrFail($id);
        $order->status = $status;
        switch ($status) {
            case 'paid':
                $order->paid_at = now();
                break;
            case 'sent':
                $order->sent_at = now();
                break;
            case 'arrived':
                $order->arrived_at = now();
                break;
            case 'canceled':
                $order->canceled_at = now();
                break;
        }
        $order->save();
        return $order;
    }

    public function cancel(Request $request, int $id): void
    {
        DB::transaction(function () use ($id, $request) {
            $order = Order::findOrFail($id)->lockForUpdate();
            if ($request->user()->id !== $order->user_id) {
                throw new AuthorizationException('This is not your order.');
            }
            $order->status = "canceled";
            $order->cancelled_at = now();
            $order->save();
            $orderItems = $order->items()->with('product')->get();

            foreach ($orderItems as $orderItem) {
                $product = Product::lockForUpdate()->find($orderItem->product_id);
                $quantity = $product->quantity + $orderItem->quantity;
                $product->quantity = $quantity;
                $product->save();
            }
        });
    }
}