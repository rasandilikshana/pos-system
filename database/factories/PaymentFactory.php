<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'method' => Payment::METHOD_CASH,
            'amount' => fake()->randomFloat(2, 100, 5000),
            'reference' => null,
            'received_by' => User::factory(),
        ];
    }

    public function card(): self
    {
        return $this->state([
            'method' => Payment::METHOD_CARD,
            'reference' => 'TXN-'.fake()->bothify('??######'),
        ]);
    }
}
