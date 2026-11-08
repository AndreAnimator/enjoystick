<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::exists()) {
            $this->command?->warn('Banco já populado: seeders ignorados. Use `php artisan migrate:fresh --seed` para recriar.');
            return;
        }

        DB::transaction(function () {
            $this->call([
                UserSeeder::class,
                CategorySeeder::class,
                ProductSeeder::class,
                AddressSeeder::class, // depende dos usuários
                CartSeeder::class,    // depende de usuários e produtos
                OrderSeeder::class,   // depende de usuários, endereços e produtos
            ]);
        });
    }
}