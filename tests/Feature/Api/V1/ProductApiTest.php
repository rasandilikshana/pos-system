<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['products.view']);

    $this->category = Category::factory()->create(['name' => 'Beverages']);
    $this->supplier = Supplier::factory()->create();
});

test('lists active products with category loaded', function () {
    Product::factory()->count(3)->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'is_active' => true,
    ]);

    getJson('/api/v1/products')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure(['data' => [['id', 'sku', 'name', 'unit_price', 'category' => ['id', 'name']]], 'links', 'meta']);
});

test('excludes inactive products from listing', function () {
    Product::factory()->count(2)->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'is_active' => true,
    ]);

    Product::factory()->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'is_active' => false,
    ]);

    getJson('/api/v1/products')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

test('filters by barcode', function () {
    $hit = Product::factory()->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'barcode' => '1234567890123',
        'is_active' => true,
    ]);

    Product::factory()->count(4)->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'barcode' => '9999999999999',
        'is_active' => true,
    ]);

    getJson('/api/v1/products?barcode=1234567890123')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $hit->id);
});

test('filters by search query on name', function () {
    Product::factory()->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'name' => 'Coca Cola 1L',
        'is_active' => true,
    ]);

    Product::factory()->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'name' => 'Pepsi 1L',
        'is_active' => true,
    ]);

    getJson('/api/v1/products?q=coca')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Coca Cola 1L');
});
