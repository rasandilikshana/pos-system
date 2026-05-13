<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 50, 1500);
        $markup = fake()->randomFloat(2, 1.2, 2.5);

        return [
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'sku' => 'SKU-'.strtoupper(fake()->unique()->bothify('??###')),
            'barcode' => fake()->unique()->ean13(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'unit_price' => round($cost * $markup, 2),
            'cost_price' => $cost,
            'current_stock' => fake()->numberBetween(10, 100),
            'reorder_level' => 10,
            'is_active' => true,
        ];
    }

    public function lowStock(): self
    {
        return $this->state(['current_stock' => 3, 'reorder_level' => 10]);
    }

    public function outOfStock(): self
    {
        return $this->state(['current_stock' => 0]);
    }

    public function archived(): self
    {
        return $this->state(['is_active' => false]);
    }
}
