<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RoleSeeder::class);
});

test('login returns a token for valid active user', function () {
    $user = User::factory()->create(['email' => 'cashier@pos.test', 'password' => 'password', 'is_active' => true]);
    $user->assignRole('cashier');

    postJson('/api/v1/auth/login', ['email' => 'cashier@pos.test', 'password' => 'password'])
        ->assertOk()
        ->assertJsonStructure(['access_token', 'token_type', 'user' => ['id', 'email', 'roles']])
        ->assertJsonPath('user.roles.0', 'cashier');
});

test('login fails with wrong password', function () {
    User::factory()->create(['email' => 'cashier@pos.test', 'password' => 'password']);

    postJson('/api/v1/auth/login', ['email' => 'cashier@pos.test', 'password' => 'wrong'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('login fails for inactive user', function () {
    User::factory()->create(['email' => 'x@pos.test', 'password' => 'password', 'is_active' => false]);

    postJson('/api/v1/auth/login', ['email' => 'x@pos.test', 'password' => 'password'])
        ->assertUnprocessable();
});

test('protected endpoint returns 401 without token', function () {
    getJson('/api/v1/products')->assertUnauthorized();
});
