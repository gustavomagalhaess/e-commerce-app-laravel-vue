<?php

namespace App\Providers;

use App\Domains\Auth\Repositories\UserRepository;
use App\Domains\Auth\Services\AuthService;
use App\Domains\Cart\Repositories\CartRepository;
use App\Domains\Cart\Services\CartService;
use App\Domains\Catalog\Models\Product;
use App\Domains\Orders\Repositories\OrderRepository;
use App\Domains\Orders\Services\OrderService;
use App\Domains\Catalog\Policies\ProductPolicy;
use App\Domains\Catalog\Repositories\CategoryRepository;
use App\Domains\Catalog\Repositories\ProductRepository;
use App\Domains\Catalog\Services\ProductService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class);
        $this->app->bind(AuthService::class);
        $this->app->bind(CategoryRepository::class);
        $this->app->bind(ProductRepository::class);
        $this->app->bind(ProductService::class);
        $this->app->bind(CartRepository::class);
        $this->app->bind(CartService::class);
        $this->app->bind(OrderRepository::class);
        $this->app->bind(OrderService::class);
    }

    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
