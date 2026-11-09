<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class OrderService
{
    private const STATUS_TRANSITIONS = [
        'waiting'  => ['paid', 'canceled'],
        'paid'     => ['sent'],
        'sent'     => ['arrived'],
        'arrived'  => [],
        'canceled' => [],
    ];

    private function restoreStock(Order $order): void
    {
        foreach ($order->items()->get() as $item) {
            Product::whereKey($item->product_id)->increment('stock', $item->quantity);
        }
    }

    public function create(array $data, Request $request): Order
    {
        return DB::transaction(function () use ($data, $request) {
            $order = Order::create($data);
            // Dava pra pegar por token também
            // TODO acho que não salvou como decimal
            $cart = Cart::where('user_id', $request->user()->id)->lockForUpdate()->first();
            if (!$cart || $cart->items()->doesntExist()) {
                throw ValidationException::withMessages([
                    'message' => 'Pedido feito com carrinho vazio/inexistente.'
                ]);
            }
            $cartItems = $cart->items()->with('product')->orderBy('product_id')->get();
            $subtotal = '0.00';
            $shippingCost = '10.00';
            
            foreach ($cartItems as $cartItem) {
                $product = Product::lockForUpdate()->find($cartItem->product_id);
                $name = $product?->name ?? "#{$cartItem->product_id}";
                if ($cartItem->quantity < 1) {
                    throw ValidationException::withMessages([
                        'message' => "Quantidade inválida para \"{$name}\" no carrinho."
                    ]);
                }
                if (!$product || !$product->active) {
                    throw ValidationException::withMessages([
                        'message' => "O produto \"{$name}\" está indisponível."
                    ]);
                }
                if ($product->stock < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'message' => "Estoque insuficiente para \"{$name}\": disponível {$product->stock}, no carrinho {$cartItem->quantity}."
                    ]);
                }
                $product->stock -= $cartItem->quantity;
                $product->save();
                $lineTotal = bcmul((string) $product->price, (string) $cartItem->quantity, 2);
                $subtotal = bcadd($subtotal, $lineTotal, 2);

                
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem->quantity,
                ]);
            }
            $order->subtotal = $subtotal;
            $order->shipping_cost = $shippingCost;
            $order->total = bcadd($subtotal, $shippingCost, 2);
            $order->save();
            $cart->items()->delete();
            $cart->delete();
            return $order->load('items');;
        });
    }

    public function listUserOrders(Request $request): Collection
    {
        return Order::with('items')->where('user_id', $request->user()->id)->get();  
    }

    public function listAll(string $column_name = 'created_at', string $order = 'asc', ?string $status, ?string $search): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $perpage = 4;

        $query = Order::with('items');
        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->items()->whereLike('name', "%$search%", caseSensitive: false);
        }
        $query->orderBy($column_name, $order);

        return $query->paginate($perpage);
    }

    public function findById(Request $request, int $id): ?Order
    {
        $order = Order::with('items')->findOrFail($id);
        if ($request->user()->id !== $order->user_id && $request->user()->role !== "admin") {
            throw new AuthorizationException('This is not your order.');
            return null;
        }
        return $order;
    }

    public function pay(Request $request, int $id): Order
    {
        $order = Order::findOrFail($id);
        if ($request->user()->id !== $order->user_id && $request->user()->role !== "admin") {
            throw new AuthorizationException('This is not your order.');
        }
        if ($order->status !== "waiting") {
            throw new ConflictHttpException(
                "Transição inválida: o pedido está '{$order->status}' e não pode ser pago."
            );
        }
        $order->status = "paid";
        $order->paid_at = now();
        $order->save();
        return $order;
    }

    public function update(array $status_array, int $id): Order
    {
        $status = $status_array['status'];
 
        return DB::transaction(function () use ($status, $id) {
            $order = Order::lockForUpdate()->findOrFail($id);
 
            $allowed = self::STATUS_TRANSITIONS[$order->status] ?? [];
            if (!in_array($status, $allowed, true)) {
                throw new ConflictHttpException(
                    "Transição inválida: o pedido está '{$order->status}' e não pode ir para '{$status}'."
                );
            }
 
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
                    $order->cancelled_at = now();
                    $this->restoreStock($order);
                    break;
            }
            $order->save();
 
            return $order;
        });
    }

    public function cancel(Request $request, int $id): order
    {
        // TODO cheque se foi pago
        return DB::transaction(function () use ($request, $id) {
            $order = Order::lockForUpdate()->findOrFail($id);
            if ($request->user()->id !== $order->user_id && $request->user()->role !== "admin") {
                throw new AuthorizationException('This is not your order.');
            }
            if ($order->status !== "waiting") {
                throw ValidationException::withMessages([
                    'message' => 'Order cannot be cancelled anymore.'
                ]);
            }
            $order->status = "canceled";
            $order->cancelled_at = now();
            $order->save();
            $orderItems = $order->items()->with('product')->get();

            foreach ($orderItems as $orderItem) {
                $product = Product::lockForUpdate()->find($orderItem->product_id);
                $quantity = $product->stock + $orderItem->quantity;
                $product->stock = $quantity;
                $product->save();
            }
            return $order->load('items');
        });
    }
}