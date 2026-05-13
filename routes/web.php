<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\Login;
use App\Livewire\Categories;
use App\Livewire\Customers;
use App\Livewire\Dashboard;
use App\Livewire\Products;
use App\Livewire\Suppliers;
use App\Livewire\Users;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/dashboard', Dashboard\Index::class)->name('dashboard');

    Route::get('/products', Products\Index::class)->name('products.index');
    Route::get('/products/create', Products\Edit::class)->name('products.create');
    Route::get('/products/{product}', Products\Edit::class)->name('products.edit');

    Route::get('/categories', Categories\Index::class)->name('categories.index');
    Route::get('/categories/create', Categories\Edit::class)->name('categories.create');
    Route::get('/categories/{category}', Categories\Edit::class)->name('categories.edit');

    Route::get('/suppliers', Suppliers\Index::class)->name('suppliers.index');
    Route::get('/suppliers/create', Suppliers\Edit::class)->name('suppliers.create');
    Route::get('/suppliers/{supplier}', Suppliers\Edit::class)->name('suppliers.edit');

    Route::get('/customers', Customers\Index::class)->name('customers.index');
    Route::get('/customers/create', Customers\Edit::class)->name('customers.create');
    Route::get('/customers/{customer}', Customers\Edit::class)->name('customers.edit');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', Users\Index::class)->name('users.index');
        Route::get('/users/create', Users\Edit::class)->name('users.create');
        Route::get('/users/{user}', Users\Edit::class)->name('users.edit');
    });
});
