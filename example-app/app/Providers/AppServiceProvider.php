<?php

namespace App\Providers;

use App\Contract\CategoryRepositoryInterface;
use App\Contract\EmployeeRepositoryInterface;
use App\Contract\OrderRepositoryInterface;
use App\Contract\ProductRepositoryInterface;
use App\Contract\ProductVariantRepointerface;
use App\Contract\VariantRepositoryInterface;
use App\Repositories\CategoryEloquentRepository;
use App\Repositories\EmployeeEloquentRepository;
use App\Repositories\OrderEloquentRepository;
use App\Repositories\ProductEloquentRepository;
use App\Repositories\ProductVariantEloquentRepo;
use App\Repositories\VariantEloquentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryEloquentRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductEloquentRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderEloquentRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeEloquentRepository::class);
        $this->app->bind(ProductVariantRepointerface::class, ProductVariantEloquentRepo::class);
        $this->app->bind(VariantRepositoryInterface::class, VariantEloquentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
