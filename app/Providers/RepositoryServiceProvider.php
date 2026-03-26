<?php

namespace App\Providers;

use App\Contracts\ProductIncomingRepositoryInterface;
use App\Contracts\ProductOutgoingRepositoryInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Contracts\ProductTypeRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Repositories\ProductIncomingRepository;
use App\Repositories\ProductOutgoingRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductTypeRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Product Type
        $this->app->bind(ProductTypeRepositoryInterface::class, ProductTypeRepository::class);

        // Product
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

        // Product Incoming
        $this->app->bind(ProductIncomingRepositoryInterface::class, ProductIncomingRepository::class);

        // Product Outgoing
        $this->app->bind(ProductOutgoingRepositoryInterface::class, ProductOutgoingRepository::class);

        // User
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
