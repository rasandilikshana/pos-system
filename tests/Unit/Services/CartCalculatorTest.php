<?php

use App\Services\CartCalculator;

beforeEach(function () {
    $this->calculator = new CartCalculator;
});

test('calculates subtotal, tax, and total for a simple cart', function () {
    $totals = $this->calculator->calculate(
        items: [
            ['unit_price' => 100.0, 'quantity' => 2],
            ['unit_price' => 50.0,  'quantity' => 1],
        ],
        discount: 0,
        taxRate: 0.05,
    );

    expect($totals->subtotal)->toBe(250.0)
        ->and($totals->discount)->toBe(0.0)
        ->and($totals->tax)->toBe(12.5)
        ->and($totals->total)->toBe(262.5);
});

test('applies a flat discount before tax', function () {
    $totals = $this->calculator->calculate(
        items: [['unit_price' => 200.0, 'quantity' => 1]],
        discount: 50.0,
        taxRate: 0.10,
    );

    expect($totals->subtotal)->toBe(200.0)
        ->and($totals->discount)->toBe(50.0)
        ->and($totals->tax)->toBe(15.0)
        ->and($totals->total)->toBe(165.0);
});

test('caps discount at subtotal so total never goes negative', function () {
    $totals = $this->calculator->calculate(
        items: [['unit_price' => 100.0, 'quantity' => 1]],
        discount: 500.0,
        taxRate: 0.05,
    );

    expect($totals->subtotal)->toBe(100.0)
        ->and($totals->discount)->toBe(100.0)
        ->and($totals->total)->toBe(0.0);
});

test('rejects negative discount', function () {
    $this->calculator->calculate(
        items: [['unit_price' => 10.0, 'quantity' => 1]],
        discount: -5.0,
    );
})->throws(InvalidArgumentException::class, 'Discount cannot be negative.');

test('rejects tax rate outside 0..1 range', function () {
    $this->calculator->calculate(
        items: [['unit_price' => 10.0, 'quantity' => 1]],
        taxRate: 1.5,
    );
})->throws(InvalidArgumentException::class, 'Tax rate must be between 0 and 1.');

test('rejects item with quantity below 1', function () {
    $this->calculator->calculate(
        items: [['unit_price' => 10.0, 'quantity' => 0]],
    );
})->throws(InvalidArgumentException::class, 'quantity must be at least 1');

test('rejects item with missing fields', function () {
    $this->calculator->calculate(
        items: [['unit_price' => 10.0]],
    );
})->throws(InvalidArgumentException::class, 'missing unit_price or quantity');

test('zero tax rate produces tax = 0 and total = subtotal - discount', function () {
    $totals = $this->calculator->calculate(
        items: [['unit_price' => 100.0, 'quantity' => 3]],
        discount: 30.0,
        taxRate: 0,
    );

    expect($totals->tax)->toBe(0.0)
        ->and($totals->total)->toBe(270.0);
});
