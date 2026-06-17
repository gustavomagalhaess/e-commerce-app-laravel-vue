<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Domains\Auth\Repositories\UserRepository;
use App\Domains\Auth\Services\AuthService;
use App\Domains\Cart\Repositories\CartRepository;
use App\Domains\Cart\Repositories\Contracts\CartRepositoryInterface;
use App\Domains\Cart\Services\CartService;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Policies\ProductPolicy;
use App\Domains\Catalog\Repositories\CategoryRepository;
use App\Domains\Catalog\Repositories\Contracts\CategoryRepositoryInterface;
use App\Domains\Catalog\Repositories\Contracts\ProductRepositoryInterface;
use App\Domains\Catalog\Repositories\ProductRepository;
use App\Domains\Catalog\Services\ProductService;
use App\Domains\Orders\Repositories\Contracts\OrderRepositoryInterface;
use App\Domains\Orders\Repositories\OrderRepository;
use App\Domains\Orders\Services\OrderService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        $this->app->bind(AuthService::class);
        $this->app->bind(CartService::class);
        $this->app->bind(ProductService::class);
        $this->app->bind(OrderService::class);
    }

    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
