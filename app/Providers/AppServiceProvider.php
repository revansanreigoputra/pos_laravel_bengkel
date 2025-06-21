<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\Interface\CategoryRepositoryInterface;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\RoleRepositoryInterface;
use App\Repositories\Interface\SupplierRepositoryInterface;
use App\Repositories\Interface\UserRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\UserRepository;
use App\Services\CategoryService;
use App\Services\CustomerService;
use App\Services\Interface\CategoryServiceInterface;
use App\Services\Interface\CustomerServiceInterface;
use App\Services\Interface\RoleServiceInterface;
use App\Services\Interface\SupplierServiceInterface;
use App\Services\Interface\UserServiceInterface;
use App\Services\RoleService;
use App\Services\SupplierService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $bindings = [
            RoleServiceInterface::class => RoleService::class,
            RoleRepositoryInterface::class => RoleRepository::class,
            CategoryServiceInterface::class => CategoryService::class,
            CategoryRepositoryInterface::class => CategoryRepository::class,
            SupplierServiceInterface::class => SupplierService::class,
            SupplierRepositoryInterface::class => SupplierRepository::class,
            CustomerServiceInterface::class => CustomerService::class,
            CustomerRepositoryInterface::class => CustomerRepository::class,
            UserServiceInterface::class => UserService::class,
            UserRepositoryInterface::class => UserRepository::class,
        ];

        foreach ($bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
