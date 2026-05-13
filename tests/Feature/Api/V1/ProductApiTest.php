<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['products.view', 'products.manage']);

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

test('POST /products creates with valid payload', function () {
    postJson('/api/v1/products', [
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'sku' => 'NEW-001',
        'name' => 'Test Product',
        'unit_price' => 199.50,
        'cost_price' => 100,
    ])
        ->assertCreated()
        ->assertJsonPath('data.sku', 'NEW-001')
        ->assertJsonPath('data.name', 'Test Product');
});

test('POST /products rejects duplicate SKU', function () {
    Product::factory()->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'sku' => 'DUP-001',
    ]);

    postJson('/api/v1/products', [
        'category_id' => $this->category->id,
        'sku' => 'DUP-001',
        'name' => 'X',
        'unit_price' => 10,
        'cost_price' => 5,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['sku']);
});

test('PATCH /products/{id} updates price', function () {
    $p = Product::factory()->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'unit_price' => 100,
    ]);

    patchJson("/api/v1/products/{$p->id}", ['unit_price' => 250.5])
        ->assertOk()
        ->assertJsonPath('data.unit_price', 250.5);
});

test('DELETE /products/{id} soft-deletes', function () {
    $p = Product::factory()->create([
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
    ]);

    deleteJson("/api/v1/products/{$p->id}")->assertNoContent();

    expect(Product::find($p->id))->toBeNull()
        ->and(Product::withTrashed()->find($p->id))->not->toBeNull();
});

test('write endpoints require products.manage ability', function () {
    Sanctum::actingAs(User::factory()->create(), ['products.view']);

    postJson('/api/v1/products', [
        'category_id' => $this->category->id,
        'sku' => 'X-1',
        'name' => 'X',
        'unit_price' => 10,
        'cost_price' => 5,
    ])->assertForbidden();
});
