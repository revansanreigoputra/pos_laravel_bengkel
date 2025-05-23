<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

    Route::prefix('supplier')->group(function () {
        Route::middleware('permission:supplier.view')->get('/', [SupplierController::class, 'index'])->name('supplier.index');
        Route::middleware('permission:supplier.store')->post('/', [SupplierController::class, 'store'])->name('supplier.store');
        Route::middleware('permission:supplier.update')->put('/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::middleware('permission:supplier.delete')->delete('/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    });
});

require __DIR__ . '/auth.php';
