<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Beverages',  'slug' => 'beverages',  'description' => 'Drinks, juices, and water'],
            ['name' => 'Snacks',     'slug' => 'snacks',     'description' => 'Chips, biscuits, and chocolates'],
            ['name' => 'Groceries',  'slug' => 'groceries',  'description' => 'Rice, flour, lentils, and spices'],
            ['name' => 'Dairy',      'slug' => 'dairy',      'description' => 'Milk, yoghurt, cheese, butter'],
            ['name' => 'Household',  'slug' => 'household',  'description' => 'Cleaning supplies and toiletries'],
            ['name' => 'Stationery', 'slug' => 'stationery', 'description' => 'Pens, notebooks, paper'],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'description' => $data['description'], 'is_active' => true]
            );
        }
    }
}
