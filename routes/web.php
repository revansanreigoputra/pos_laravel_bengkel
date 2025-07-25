<?php

use App\Http\Controllers\SupplierSparepartStockController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JenisKendaraanController;
use App\Models\Supplier;

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

    Route::prefix('transaction')->group(function () {
        Route::middleware('permission:transaction.view')->get('/', [TransactionController::class, 'index'])->name('transaction.index');
        Route::middleware('permission:transaction.store')->post('/', [TransactionController::class, 'store'])->name('transaction.store');
        Route::middleware('permission:transaction.update')->put('/{transaction}', [TransactionController::class, 'update'])->name('transaction.update');
        Route::middleware('permission:transaction.delete')->delete('/{transaction}', [TransactionController::class, 'destroy'])->name('transaction.destroy');
    });

    Route::prefix('jenis-kendaraan')->group(function () {
        Route::middleware('permission:jenis-kendaraan.view')->get('/', [JenisKendaraanController::class, 'index'])->name('jenis-kendaraan.index');
        Route::middleware('permission:jenis-kendaraan.store')->post('/', [JenisKendaraanController::class, 'store'])->name('jenis-kendaraan.store');
        Route::middleware('permission:jenis-kendaraan.update')->put('/{jenis_kendaraan}', [JenisKendaraanController::class, 'update'])->name('jenis-kendaraan.update');
        Route::middleware('permission:jenis-kendaraan.delete')->delete('/{jenis_kendaraan}', [JenisKendaraanController::class, 'destroy'])->name('jenis-kendaraan.destroy');
    });

});

require __DIR__ . '/auth.php';

// dummy route to test the route
// routes/web.php

Route::get('/sparepart', [SparepartController::class, 'index'])
->name('sparepart.index');
Route::get('/sparepart/create', [SparepartController::class, 'create'])
->name('sparepart.create');
Route::post('/sparepart', [SparepartController::class, 'store'])
->name('sparepart.store');
Route::resource('sparepart', SparepartController::class);

// Route::resource('service', \App\Http\Controllers\ServiceController::class);

// route for sparepart stock handling
Route::resource('stock-handle', SupplierSparepartStockController::class)
    ->parameters([
        'stock-handle' => 'stock'
    ])
    ->names([
        'index' => 'stock-handle.index',
        'create' => 'stock-handle.create',
        'store' => 'stock-handle.store',
        'show' => 'stock-handle.show',
        'edit' => 'stock-handle.edit',
        'update' => 'stock-handle.update',
        'destroy' => 'stock-handle.destroy',
    ]);
Route::post('/stock-handle/quick-store', [SupplierSparepartStockController::class, 'quickStore'])->name('stock-handle.quick-store');


// Route::resource('stock-handle', SupplierSparepartStockController::class);


// export pdf
Route::get('/supplier/export-pdf', [SupplierController::class, 'exportPDF'])->name('supplier.export-pdf');
Route::get('/customer/export-pdf', [CustomerController::class, 'exportPDF'])->name('customer.export-pdf');


