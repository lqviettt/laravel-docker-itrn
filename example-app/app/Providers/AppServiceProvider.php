<?php

namespace App\Providers;

use App\Repositories\CategoryEloquentRepository;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\EmployeeEloquentRepository;
use App\Repositories\EmployeeRepositoryInterface;
use App\Repositories\OrderEloquentRepository;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\ProductEloquentRepository;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class,CategoryEloquentRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductEloquentRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderEloquentRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeEloquentRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
