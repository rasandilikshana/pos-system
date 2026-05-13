<?php

use App\Models\Supplier;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('GET /suppliers lists suppliers', function () {
    Sanctum::actingAs($this->user, ['suppliers.view']);
    Supplier::factory()->count(2)->create();

    getJson('/api/v1/suppliers')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

test('POST /suppliers creates with valid data', function () {
    Sanctum::actingAs($this->user, ['suppliers.manage']);

    postJson('/api/v1/suppliers', [
        'name' => 'Acme Inc',
        'contact_name' => 'John',
        'email' => 'john@acme.test',
        'phone' => '0771234567',
    ])
        ->assertCreated()
        ->assertJsonPath('data.email', 'john@acme.test');
});

test('POST /suppliers rejects duplicate email', function () {
    Sanctum::actingAs($this->user, ['suppliers.manage']);
    Supplier::factory()->create(['email' => 'dup@s.test']);

    postJson('/api/v1/suppliers', ['name' => 'X', 'email' => 'dup@s.test'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('PATCH /suppliers/{id} updates name', function () {
    Sanctum::actingAs($this->user, ['suppliers.manage']);
    $s = Supplier::factory()->create(['name' => 'Old']);

    patchJson("/api/v1/suppliers/{$s->id}", ['name' => 'New'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New');
});

test('DELETE /suppliers/{id} soft-deletes', function () {
    Sanctum::actingAs($this->user, ['suppliers.manage']);
    $s = Supplier::factory()->create();

    deleteJson("/api/v1/suppliers/{$s->id}")->assertNoContent();

    expect(Supplier::find($s->id))->toBeNull();
});

test('GET /suppliers rejects token without suppliers.view ability', function () {
    Sanctum::actingAs($this->user, ['products.view']);

    getJson('/api/v1/suppliers')->assertForbidden();
});
