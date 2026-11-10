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
        $admin = User::firstOrCreate(
            ['email' => 'gestorStock@enjoystick.com',
                'name' => 'Gestor',
                'password' => 'controleSt0q'
            ]
        );

        $admin->forceFill(['role' => 'admin'])->save();

        $users = [
            ['name' => 'Adriano Vasconscelos', 'email' => 'adrivasco@gmail.com', 'password' => 'V4sc0'],
            ['name' => 'Pedro Hercules', 'email' => 'ph.grego@outlook.com', 'password' => 'ph123ph'],
            ['name' => 'André Miranda', 'email' => 'estupidezhonesta@proton.com', 'password' => 'stupid123'],
        ];

        foreach($users as $user) {
            User::firstOrCreate($user);
        }
    }
}
