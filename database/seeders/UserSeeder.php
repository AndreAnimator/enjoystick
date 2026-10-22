<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Gestor', 'email' => 'gestorStock@enjoystick.com', 'password' => 'controleSt0q', 'role' => 'admin'],
            ['name' => 'Adriano Vasconscelos', 'email' => 'adrivasco@gmail.com', 'password' => 'V4sc0'],
            ['name' => 'Pedro Hercules', 'email' => 'ph.grego@outlook.com', 'password' => 'ph123ph'],
            ['name' => 'André Miranda', 'email' => 'estupidezhonesta@proton.com', 'password' => 'stupid123'],
        ];

        foreach($users as $user) {
            User::create($user);
        };  
    
    }
}
