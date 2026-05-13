<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;

use function Pest\Laravel\get;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RoleSeeder::class);
});

test('login page renders', function () {
    get('/login')->assertOk()->assertSeeLivewire(Login::class);
});

test('valid credentials log the user in and redirect to dashboard', function () {
    $user = User::factory()->create([
        'email' => 'a@pos.test',
        'password' => 'password',
        'is_active' => true,
    ]);
    $user->assignRole('admin');

    Livewire::test(Login::class)
        ->set('form.email', 'a@pos.test')
        ->set('form.password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(auth()->id())->toBe($user->id);
});

test('wrong password keeps user logged out and shows validation error', function () {
    User::factory()->create(['email' => 'a@pos.test', 'password' => 'password']);

    Livewire::test(Login::class)
        ->set('form.email', 'a@pos.test')
        ->set('form.password', 'wrong')
        ->call('login')
        ->assertHasErrors(['form.email']);

    expect(auth()->check())->toBeFalse();
});

test('suspended user cannot log in', function () {
    User::factory()->create([
        'email' => 'sus@pos.test',
        'password' => 'password',
        'is_active' => false,
    ]);

    Livewire::test(Login::class)
        ->set('form.email', 'sus@pos.test')
        ->set('form.password', 'password')
        ->call('login')
        ->assertHasErrors(['form.email']);
});
