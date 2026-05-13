<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:6,1')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::middleware('ability:products.view')->group(function () {
            Route::get('/products', [ProductController::class, 'index']);
            Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product');
        });
        Route::middleware('ability:products.manage')->group(function () {
            Route::post('/products', [ProductController::class, 'store']);
            Route::patch('/products/{product}', [ProductController::class, 'update'])->whereNumber('product');
            Route::delete('/products/{product}', [ProductController::class, 'destroy'])->whereNumber('product');
        });

        Route::middleware('ability:categories.view')->group(function () {
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::get('/categories/{category}', [CategoryController::class, 'show'])->whereNumber('category');
        });
        Route::middleware('ability:categories.manage')->group(function () {
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::patch('/categories/{category}', [CategoryController::class, 'update'])->whereNumber('category');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->whereNumber('category');
        });

        Route::middleware('ability:suppliers.view')->group(function () {
            Route::get('/suppliers', [SupplierController::class, 'index']);
            Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->whereNumber('supplier');
        });
        Route::middleware('ability:suppliers.manage')->group(function () {
            Route::post('/suppliers', [SupplierController::class, 'store']);
            Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update'])->whereNumber('supplier');
            Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->whereNumber('supplier');
        });

        Route::middleware('ability:customers.view')->group(function () {
            Route::get('/customers', [CustomerController::class, 'index']);
            Route::get('/customers/{customer}', [CustomerController::class, 'show'])->whereNumber('customer');
        });
        Route::middleware('ability:customers.manage')->group(function () {
            Route::post('/customers', [CustomerController::class, 'store']);
            Route::patch('/customers/{customer}', [CustomerController::class, 'update'])->whereNumber('customer');
            Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->whereNumber('customer');
        });

        Route::middleware('ability:users.manage')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{user}', [UserController::class, 'show'])->whereNumber('user');
            Route::post('/users', [UserController::class, 'store']);
            Route::patch('/users/{user}', [UserController::class, 'update'])->whereNumber('user');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->whereNumber('user');
        });
    });
});
