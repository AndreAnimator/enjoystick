<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Modified Controllers'],
            ['name' => 'Switches and Joysticks'],
            ['name' => 'Adaptive Controllers'],
            ['name' => 'Controllers'],
        ];
        
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
