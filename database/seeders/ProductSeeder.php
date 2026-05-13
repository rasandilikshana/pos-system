<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        Product::factory()
            ->count(40)
            ->state(fn () => [
                'category_id' => $categories->random()->id,
                'supplier_id' => $suppliers->random()->id,
            ])
            ->create();

        Product::factory()->count(3)->lowStock()->state(fn () => [
            'category_id' => $categories->random()->id,
            'supplier_id' => $suppliers->random()->id,
        ])->create();
    }
}
