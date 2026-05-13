<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RoleSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RoleSeeder::class);

    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole('admin');

    $this->cashier = User::factory()->create(['is_active' => true]);
    $this->cashier->assignRole('cashier');
});

test('dashboard renders for authenticated active user', function () {
    actingAs($this->admin);

    get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Dashboard');
});

test('guest redirected to login when hitting dashboard', function () {
    get(route('dashboard'))->assertRedirect(route('login'));
});

test('products index renders for cashier (has products.view)', function () {
    actingAs($this->cashier);

    Product::factory()->count(3)->create([
        'category_id' => Category::factory(),
        'supplier_id' => Supplier::factory(),
    ]);

    get(route('products.index'))->assertOk()->assertSeeText('Products');
});

test('categories index renders for cashier', function () {
    actingAs($this->cashier);
    Category::factory()->count(2)->create();

    get(route('categories.index'))->assertOk()->assertSeeText('Categories');
});

test('suppliers index 403s for cashier (no suppliers.view permission)', function () {
    actingAs($this->cashier);

    get(route('suppliers.index'))->assertForbidden();
});

test('suppliers index renders for admin', function () {
    actingAs($this->admin);
    Supplier::factory()->count(2)->create();

    get(route('suppliers.index'))->assertOk()->assertSeeText('Suppliers');
});

test('customers index renders for cashier', function () {
    actingAs($this->cashier);
    Customer::factory()->count(2)->create();

    get(route('customers.index'))->assertOk()->assertSeeText('Customers');
});

test('users index renders for admin only', function () {
    actingAs($this->admin);

    get(route('users.index'))->assertOk()->assertSeeText('Staff');
});

test('users index 403s for cashier', function () {
    actingAs($this->cashier);

    get(route('users.index'))->assertForbidden();
});
