<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            AddressSeeder::class, // depende dos usuários
            CartSeeder::class,    // depende de usuários e produtos
            OrderSeeder::class,   // depende de usuários, endereços e produtos
        ]);
    }
}
