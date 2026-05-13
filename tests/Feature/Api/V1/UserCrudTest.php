<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RoleSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

test('GET /users requires users.manage ability', function () {
    Sanctum::actingAs($this->admin, ['users.manage']);
    User::factory()->count(2)->create();

    getJson('/api/v1/users')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'email', 'roles']]]);
});

test('GET /users rejected without users.manage ability', function () {
    $cashier = User::factory()->create();
    Sanctum::actingAs($cashier, ['customers.view']);

    getJson('/api/v1/users')->assertForbidden();
});

test('POST /users creates and assigns role + hashes password', function () {
    Sanctum::actingAs($this->admin, ['users.manage']);

    postJson('/api/v1/users', [
        'name' => 'New Cashier',
        'email' => 'newcash@test.com',
        'password' => 'secret-pass-12',
        'role' => 'cashier',
    ])
        ->assertCreated()
        ->assertJsonPath('data.email', 'newcash@test.com');

    $u = User::where('email', 'newcash@test.com')->first();
    expect($u)->not->toBeNull()
        ->and($u->hasRole('cashier'))->toBeTrue()
        ->and(Hash::check('secret-pass-12', $u->password))->toBeTrue();
});

test('PATCH /users/{id} changes role and is_active', function () {
    Sanctum::actingAs($this->admin, ['users.manage']);
    $u = User::factory()->create();
    $u->assignRole('cashier');

    patchJson("/api/v1/users/{$u->id}", ['role' => 'manager', 'is_active' => false])
        ->assertOk()
        ->assertJsonPath('data.is_active', false);

    expect($u->fresh()->hasRole('manager'))->toBeTrue();
});

test('DELETE /users/{id} blocked when targeting self', function () {
    Sanctum::actingAs($this->admin, ['users.manage']);

    deleteJson("/api/v1/users/{$this->admin->id}")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'You cannot delete your own account.');
});

test('DELETE /users/{id} succeeds for other user', function () {
    Sanctum::actingAs($this->admin, ['users.manage']);
    $other = User::factory()->create();

    deleteJson("/api/v1/users/{$other->id}")->assertNoContent();
});
