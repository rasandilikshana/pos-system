<?php

use App\Models\Customer;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('GET /customers lists customers', function () {
    Sanctum::actingAs($this->user, ['customers.view']);
    Customer::factory()->count(2)->create();

    getJson('/api/v1/customers')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => [['id', 'name', 'loyalty_points']]]);
});

test('GET /customers?q= filters by name/email/phone', function () {
    Sanctum::actingAs($this->user, ['customers.view']);

    Customer::factory()->create(['name' => 'Alice']);
    Customer::factory()->create(['name' => 'Bob']);

    getJson('/api/v1/customers?q=alic')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Alice');
});

test('POST /customers creates', function () {
    Sanctum::actingAs($this->user, ['customers.manage']);

    postJson('/api/v1/customers', [
        'name' => 'Walk-in',
        'phone' => '0771112222',
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Walk-in')
        ->assertJsonPath('data.loyalty_points', 0);
});

test('PATCH /customers/{id} updates loyalty contact', function () {
    Sanctum::actingAs($this->user, ['customers.manage']);
    $c = Customer::factory()->create(['name' => 'Old']);

    patchJson("/api/v1/customers/{$c->id}", ['name' => 'New', 'phone' => '0779999999'])
        ->assertOk()
        ->assertJsonPath('data.phone', '0779999999');
});

test('DELETE /customers/{id} soft-deletes', function () {
    Sanctum::actingAs($this->user, ['customers.manage']);
    $c = Customer::factory()->create();

    deleteJson("/api/v1/customers/{$c->id}")->assertNoContent();
});
