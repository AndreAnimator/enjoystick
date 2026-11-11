<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    private const SHIPPING_COST = '10.00';

    public function run(): void
    {
        if (Order::exists()) {
            $this->command?->warn('Já existem pedidos: OrderSeeder ignorado.');
            return;
        }

        $pool = Product::where('active', 1)
            ->where('price', '>', 0)
            ->where('stock', '>=', 10)
            ->orderBy('id')
            ->skip(4)
            ->take(6)
            ->get()
            ->values();

        if ($pool->count() < 6) {
            throw new \RuntimeException('OrderSeeder precisa de 6 produtos ativos, com preço e estoque >= 10, além dos 4 do CartSeeder. Rode o ProductSeeder antes.');
        }

        $adriano = User::where('email', 'adrivasco@gmail.com')->firstOrFail();
        $pedro = User::where('email', 'ph.grego@outlook.com')->firstOrFail();
        $andre = User::where('email', 'estupidezhonesta@proton.com')->firstOrFail();

        $orders = [
            ['status' => 'waiting',  'user' => $adriano, 'address' => 0, 'days' => 1,  'items' => [[0, 1], [1, 2]]],
            ['status' => 'paid',     'user' => $pedro,   'address' => 0, 'days' => 4,  'items' => [[2, 1]]],
            ['status' => 'sent',     'user' => $andre,   'address' => 0, 'days' => 8,  'items' => [[3, 2], [4, 1]]],
            ['status' => 'arrived',  'user' => $adriano, 'address' => 1, 'days' => 20, 'items' => [[5, 1], [0, 1]]],
            ['status' => 'canceled', 'user' => $pedro,   'address' => 0, 'days' => 3,  'items' => [[1, 1]]],
        ];

        foreach ($orders as $def) {
            $address = $this->addressFor($def['user'], $def['address']);
            $createdAt = now()->subDays($def['days']);

            $subtotal = '0.00';
            foreach ($def['items'] as [$index, $quantity]) {
                $line = bcmul((string) $pool[$index]->price, (string) $quantity, 2);
                $subtotal = bcadd($subtotal, $line, 2);
            }

            $order = new Order([
                'user_id' => $def['user']->id,
                'status' => $def['status'],
                'subtotal' => $subtotal,
                'shipping_cost' => self::SHIPPING_COST,
                'total' => bcadd($subtotal, self::SHIPPING_COST, 2),
                'shipping_recipient_name' => $address->recipient_name,
                'shipping_postal_code' => $address->postal_code,
                'shipping_street' => $address->street,
                'shipping_number' => $address->number,
                'shipping_complement' => $address->complement,
                'shipping_neighborhood' => $address->neighborhood,
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,
                'shipping_country' => $address->country,
            ] + $this->timeline($def['status'], $createdAt));
            $order->created_at = $createdAt;
            $order->updated_at = $createdAt;
            $order->save();

            foreach ($def['items'] as [$index, $quantity]) {
                $product = $pool[$index];
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                ]);

                if ($def['status'] !== 'canceled') {
                    Product::whereKey($product->id)->decrement('stock', $quantity);
                }
            }
        }
    }

    private function timeline(string $status, $createdAt): array
    {
        $paidAt = $createdAt->copy()->addHours(1);
        $sentAt = $createdAt->copy()->addDay();
        $arrivedAt = $createdAt->copy()->addDays(4);

        return match ($status) {
            'waiting' => [],
            'paid' => ['paid_at' => $paidAt],
            'sent' => ['paid_at' => $paidAt, 'sent_at' => $sentAt],
            'arrived' => ['paid_at' => $paidAt, 'sent_at' => $sentAt, 'arrived_at' => $arrivedAt],
            'canceled' => ['cancelled_at' => $createdAt->copy()->addHours(3)],
        };
    }

    private function addressFor(User $user, int $index): Address
    {
        $addresses = Address::where('user_id', $user->id)->orderBy('id')->get();

        if ($addresses->isEmpty()) {
            throw new \RuntimeException("{$user->email} não tem endereço. Rode o AddressSeeder antes.");
        }

        return $addresses[$index] ?? $addresses->first();
    }
}