<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100, 5000);
        $tax = round($subtotal * 0.05, 2);
        $total = $subtotal + $tax;

        return [
            'code' => 'INV-'.fake()->unique()->numerify('######'),
            'customer_id' => null,
            'user_id' => User::factory(),
            'status' => Sale::STATUS_OPEN,
            'subtotal' => $subtotal,
            'discount' => 0,
            'tax' => $tax,
            'total' => $total,
            'amount_paid' => 0,
            'change_due' => 0,
        ];
    }

    public function withCustomer(): self
    {
        return $this->state(['customer_id' => Customer::factory()]);
    }

    public function completed(): self
    {
        return $this->state(fn (array $attrs) => [
            'status' => Sale::STATUS_COMPLETED,
            'amount_paid' => $attrs['total'],
            'completed_at' => now(),
        ]);
    }

    public function voided(): self
    {
        return $this->state(fn (array $attrs) => [
            'status' => Sale::STATUS_VOIDED,
            'voided_at' => now(),
            'voided_by' => User::factory(),
            'void_reason' => 'Customer returned',
        ]);
    }
}
