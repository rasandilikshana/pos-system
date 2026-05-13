<?php

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('GET /categories lists categories for token with categories.view', function () {
    Sanctum::actingAs($this->user, ['categories.view']);
    Category::factory()->count(3)->create();

    getJson('/api/v1/categories')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure(['data' => [['id', 'name', 'slug']], 'links', 'meta']);
});

test('GET /categories rejects token without ability', function () {
    Sanctum::actingAs($this->user, ['products.view']);

    getJson('/api/v1/categories')->assertForbidden();
});

test('POST /categories creates a category with categories.manage', function () {
    Sanctum::actingAs($this->user, ['categories.manage']);

    postJson('/api/v1/categories', ['name' => 'Drinks', 'description' => 'Beverages'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Drinks')
        ->assertJsonPath('data.slug', 'drinks');

    expect(Category::where('slug', 'drinks')->exists())->toBeTrue();
});

test('POST /categories rejects duplicate name', function () {
    Sanctum::actingAs($this->user, ['categories.manage']);
    Category::factory()->create(['name' => 'Drinks', 'slug' => 'drinks']);

    postJson('/api/v1/categories', ['name' => 'Drinks'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('PATCH /categories/{id} updates fields', function () {
    Sanctum::actingAs($this->user, ['categories.manage']);
    $category = Category::factory()->create(['name' => 'Old', 'slug' => 'old']);

    patchJson("/api/v1/categories/{$category->id}", ['name' => 'New Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name');
});

test('DELETE /categories/{id} soft-deletes the record', function () {
    Sanctum::actingAs($this->user, ['categories.manage']);
    $category = Category::factory()->create();

    deleteJson("/api/v1/categories/{$category->id}")->assertNoContent();

    expect(Category::find($category->id))->toBeNull()
        ->and(Category::withTrashed()->find($category->id))->not->toBeNull();
});
