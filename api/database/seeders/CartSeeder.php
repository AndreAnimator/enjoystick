<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        if (Cart::exists()) {
            $this->command?->warn('Já existem carrinhos: CartSeeder ignorado.');
            return;
        }

        $pool = Product::where('active', 1)
            ->where('price', '>', 0)
            ->where('stock', '>=', 10)
            ->orderBy('id')
            ->take(4)
            ->get();

        if ($pool->count() < 4) {
            throw new \RuntimeException('CartSeeder precisa de 4 produtos ativos, com preço e estoque >= 10. Rode o ProductSeeder antes.');
        }

        [$overlap, $capped, $onlyGuest, $forAdriano] = $pool->values()->all();

        $pedro = User::where('email', 'ph.grego@outlook.com')->firstOrFail();
        $adriano = User::where('email', 'adrivasco@gmail.com')->firstOrFail();

        $guestToken = Str::random(64);
        $guest = Cart::create(['user_id' => null, 'token' => $guestToken]);
        $guest->items()->create(['product_id' => $overlap->id, 'quantity' => 2]);
        $guest->items()->create(['product_id' => $capped->id, 'quantity' => 3]);
        $guest->items()->create(['product_id' => $onlyGuest->id, 'quantity' => 1]);

        $pedroCart = Cart::create(['user_id' => $pedro->id, 'token' => Str::random(64)]);
        $pedroCart->items()->create(['product_id' => $overlap->id, 'quantity' => 1]);
        $pedroCart->items()->create(['product_id' => $capped->id, 'quantity' => ((int) $capped->stock) - 1]);

        $adrianoCart = Cart::create(['user_id' => $adriano->id, 'token' => Str::random(64)]);
        $adrianoCart->items()->create(['product_id' => $forAdriano->id, 'quantity' => 2]);

        $inactive = Product::where('active', 0)->orderBy('id')->first();
        if ($inactive) {
            $adrianoCart->items()->create(['product_id' => $inactive->id, 'quantity' => 1]);
        }
        $outOfStock = Product::where('active', 1)->where('stock', 0)->orderBy('id')->first();
        if ($outOfStock) {
            $adrianoCart->items()->create(['product_id' => $outOfStock->id, 'quantity' => 1]);
        }

        $this->command?->info("Token do carrinho de visitante: {$guestToken}");
    }
}