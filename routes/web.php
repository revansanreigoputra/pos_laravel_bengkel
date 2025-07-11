<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('pages.dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard-kasir', function () {
    return view('pages.dashboard_kasir');
})->middleware(['auth', 'verified'])->name('dashboard.kasir');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role & Permission
    Route::middleware('permission:role.view')->get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::middleware('permission:role.view')->get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::middleware('permission:role.update')->put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');

    Route::prefix('kategori')->group(function () {
        Route::middleware('permission:category.view')->get('/', [CategoryController::class, 'index'])->name('category.index');
        Route::middleware('permission:category.store')->post('/', [CategoryController::class, 'store'])->name('category.store');
        Route::middleware('permission:category.update')->put('/{category}', [CategoryController::class, 'update'])->name('category.update');
        Route::middleware('permission:category.delete')->delete('/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
    });

    Route::prefix('service')->group(function () {
        Route::middleware('permission:service.view')->get('/', [ServiceController::class, 'index'])->name('service.index');
        Route::middleware('permission:service.store')->post('/', [ServiceController::class, 'store'])->name('service.store');
        Route::middleware('permission:service.update')->put('/{service}', [ServiceController::class, 'update'])->name('service.update');
        Route::middleware('permission:service.delete')->delete('/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');
    });

    Route::prefix('supplier')->group(function () {
        Route::middleware('permission:supplier.view')->get('/', [SupplierController::class, 'index'])->name('supplier.index');
        Route::middleware('permission:supplier.store')->post('/', [SupplierController::class, 'store'])->name('supplier.store');
        Route::middleware('permission:supplier.update')->put('/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::middleware('permission:supplier.delete')->delete('/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    });

    Route::prefix('konsumen')->group(function () {
        Route::middleware('permission:customer.view')->get('/', [CustomerController::class, 'index'])->name('customer.index');
        Route::middleware('permission:customer.store')->post('/', [CustomerController::class, 'store'])->name('customer.store');
        Route::middleware('permission:customer.update')->put('/{customer}', [CustomerController::class, 'update'])->name('customer.update');
        Route::middleware('permission:customer.delete')->delete('/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
    });

    Route::prefix('user')->group(function () {
        Route::middleware('permission:user.view')->get('/', [UserController::class, 'index'])->name('user.index');
        Route::middleware('permission:user.store')->post('/', [UserController::class, 'store'])->name('user.store');
        Route::middleware('permission:user.update')->put('/{user}', [UserController::class, 'update'])->name('user.update');
        Route::middleware('permission:user.delete')->delete('/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });
});

require __DIR__ . '/auth.php';

// dummy route to test the route
Route::get('/product', function() {
    return view('pages.product.index');
})->name('product.index');

Route::resource('service', \App\Http\Controllers\ServiceController::class);

Route::get('/transaction', function() {
    return view('pages.transaction.index');
})->name('transaction.index');