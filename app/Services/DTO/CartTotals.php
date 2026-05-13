<?php

namespace App\Services\DTO;

final class CartTotals
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $tax,
        public readonly float $total,
    ) {}

    /** @return array{subtotal:float,discount:float,tax:float,total:float} */
    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total' => $this->total,
        ];
    }
}
