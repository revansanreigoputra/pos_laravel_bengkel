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
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use App\Models\BengkelSetting;
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
use Illuminate\Support\Facades\Auth;

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
        View::composer('*', function ($view) {
            $user = Auth::user();
            $settings = BengkelSetting::first();

            $viewData = [];

            if ($user) {
                // Ambil 2 notifikasi terbaru untuk navbar
                $navbarNotifications = Notification::where('notifiable_type', get_class($user))
                    ->where('notifiable_id', $user->id)
                    ->latest()
                    ->take(2) // ✅ batasi 2 data saja
                    ->get();

                $unreadCount = $navbarNotifications->whereNull('read_at')->count();

                $viewData['navbarNotifications'] = $navbarNotifications;
                $viewData['unreadCount'] = $unreadCount;
            }

            if ($settings && $settings->logo_path) {
                $viewData['logo_path'] = asset('storage/' . $settings->logo_path);
            } else {
                $viewData['logo_path'] = asset('assets/logo.png');
            }

            if ($settings) {
                $viewData['nama_bengkel'] = $settings->nama_bengkel;
            } else {
                $viewData['nama_bengkel'] = 'Bengkel POS'; // Default title
            }

            $view->with($viewData);
        });
    }
}