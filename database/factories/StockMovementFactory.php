<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 50);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'type' => StockMovement::TYPE_IN,
            'quantity' => $qty,
            'balance_after' => $qty,
            'reason' => 'Initial stock',
        ];
    }

    public function out(): self
    {
        return $this->state([
            'type' => StockMovement::TYPE_OUT,
            'quantity' => -fake()->numberBetween(1, 5),
            'reason' => 'Sale',
        ]);
    }

    public function adjustment(): self
    {
        return $this->state([
            'type' => StockMovement::TYPE_ADJUSTMENT,
            'reason' => 'Stock take',
        ]);
    }
}
