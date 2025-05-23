<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\Interface\CategoryRepositoryInterface;
use App\Repositories\Interface\RoleRepositoryInterface;
use App\Repositories\Interface\SupplierRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Repositories\SupplierRepository;
use App\Services\CategoryService;
use App\Services\Interface\CategoryServiceInterface;
use App\Services\Interface\RoleServiceInterface;
use App\Services\Interface\SupplierServiceInterface;
use App\Services\RoleService;
use App\Services\SupplierService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
