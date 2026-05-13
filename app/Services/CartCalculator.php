<?php

namespace App\Services;

use App\Services\DTO\CartTotals;
use InvalidArgumentException;

class CartCalculator
{
    /**
     * Calculate totals for a cart of items.
     *
     * @param  array<int, array{unit_price:float|int|string, quantity:int}>  $items
     */
    public function calculate(array $items, float $discount = 0, float $taxRate = 0.05): CartTotals
    {
        if ($discount < 0) {
            throw new InvalidArgumentException('Discount cannot be negative.');
        }

        if ($taxRate < 0 || $taxRate > 1) {
            throw new InvalidArgumentException('Tax rate must be between 0 and 1.');
        }

        $subtotal = 0.0;

        foreach ($items as $index => $item) {
            if (! isset($item['unit_price'], $item['quantity'])) {
                throw new InvalidArgumentException("Item [{$index}] is missing unit_price or quantity.");
            }

            if ($item['quantity'] < 1) {
                throw new InvalidArgumentException("Item [{$index}] quantity must be at least 1.");
            }

            $subtotal += (float) $item['unit_price'] * (int) $item['quantity'];
        }

        $effectiveDiscount = min($discount, $subtotal);
        $afterDiscount = $subtotal - $effectiveDiscount;

        $tax = round($afterDiscount * $taxRate, 2);
        $total = round($afterDiscount + $tax, 2);

        return new CartTotals(
            subtotal: round($subtotal, 2),
            discount: round($effectiveDiscount, 2),
            tax: $tax,
            total: $total,
        );
    }
}
