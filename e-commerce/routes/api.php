<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Auth\Http\Controllers\LoginController;
use App\Domains\Auth\Http\Controllers\RegisterController;
use App\Domains\Auth\Http\Controllers\LogoutController;
use App\Domains\Auth\Http\Controllers\ProfileController;
use App\Domains\Auth\Http\Controllers\PasswordController;
use App\Domains\Catalog\Http\Controllers\ProductController;
use App\Domains\Catalog\Http\Controllers\CategoryController;
use App\Domains\Cart\Http\Controllers\CartController;
use App\Domains\Orders\Http\Controllers\OrderController;
use App\Domains\Orders\Http\Controllers\SalesController;
use App\Domains\Admin\Http\Controllers\CategoryController as AdminCategoryController;
use App\Domains\Admin\Http\Controllers\ProductController as AdminProductController;
use App\Domains\Admin\Http\Controllers\UserController as AdminUserController;

// Public auth
Route::post('/register', [RegisterController::class, '__invoke']);
Route::post('/login', [LoginController::class, '__invoke']);

// Public catalog
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, '__invoke']);
    Route::get('/user', [ProfileController::class, 'show']);
    Route::put('/user/profile', [ProfileController::class, 'update']);
    Route::put('/user/password', [PasswordController::class, '__invoke']);

    // Catalog (authenticated)
    Route::post('/products', [ProductController::class, 'store']);
    Route::match(['POST', 'PUT'], '/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::get('/my-products', [ProductController::class, 'myProducts']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/my-sales', [SalesController::class, 'index']);

    // Admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::post('/categories', [AdminCategoryController::class, 'store']);
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy']);

        Route::get('/products', [AdminProductController::class, 'index']);
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy']);

        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole']);
    });
});
