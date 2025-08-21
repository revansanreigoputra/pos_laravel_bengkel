<?php
use App\Http\Controllers\InventoryReportController;
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
use App\Http\Controllers\JenisKendaraanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PurchaseOrderController;     // Import controller baru
use App\Http\Controllers\PurchaseOrdersItemsController; // Import controller baru
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;

use Illuminate\Support\Facades\Route;

// Route untuk Dashboard
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
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');


    // Role & Permission
    Route::middleware('permission:role.view')->get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::middleware('permission:role.view')->get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::middleware('permission:role.update')->put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');

    // Kategori
    Route::prefix('kategori')->group(function () {
        Route::middleware('permission:category.view')->get('/', [CategoryController::class, 'index'])->name('category.index');
        Route::middleware('permission:category.store')->post('/', [CategoryController::class, 'store'])->name('category.store');
        Route::middleware('permission:category.update')->put('/{category}', [CategoryController::class, 'update'])->name('category.update');
        Route::middleware('permission:category.delete')->delete('/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    });

    // Service
    Route::prefix('service')->group(function () {
        Route::middleware('permission:service.view')->get('/', [ServiceController::class, 'index'])->name('service.index');
        Route::middleware('permission:service.store')->post('/', [ServiceController::class, 'store'])->name('service.store');
        Route::middleware('permission:service.update')->put('/{service}', [ServiceController::class, 'update'])->name('service.update');
        Route::middleware('permission:service.delete')->delete('/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');
        Route::middleware('permission:service.create')->get('/modal-create', [ServiceController::class, 'create'])->name('service.modal-create');
        Route::middleware('permission:service.edit')->get('/{service}/edit', [ServiceController::class, 'edit'])->name('service.edit');
    });

    // Supplier
    Route::prefix('supplier')->group(function () {
        Route::middleware('permission:supplier.view')->get('/', [SupplierController::class, 'index'])->name('supplier.index');
        Route::middleware('permission:supplier.store')->post('/', [SupplierController::class, 'store'])->name('supplier.store');
        Route::middleware('permission:supplier.update')->put('/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::middleware('permission:supplier.delete')->delete('/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    });

    // Konsumen
    Route::prefix('konsumen')->group(function () {
        Route::middleware('permission:customer.view')->get('/', [CustomerController::class, 'index'])->name('customer.index');
        Route::middleware('permission:customer.store')->post('/', [CustomerController::class, 'store'])->name('customer.store');
        Route::middleware('permission:customer.update')->put('/{customer}', [CustomerController::class, 'update'])->name('customer.update');
        Route::middleware('permission:customer.delete')->delete('/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
    });

    // User
    Route::prefix('user')->group(function () {
        Route::middleware('permission:user.view')->get('/', [UserController::class, 'index'])->name('user.index');
        Route::middleware('permission:user.store')->post('/', [UserController::class, 'store'])->name('user.store');
        Route::middleware('permission:user.update')->put('/{user}', [UserController::class, 'update'])->name('user.update');
        Route::middleware('permission:user.delete')->delete('/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    // Transaction (Penjualan)
    Route::prefix('transaction')->group(function () {
        Route::middleware('permission:transaction.view')->get('/', [TransactionController::class, 'index'])->name('transaction.index');
        Route::middleware('permission:transaction.create')->get('/create', [TransactionController::class, 'create'])->name('transaction.create');
        Route::middleware('permission:transaction.update')->get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('transaction.edit');
        Route::middleware('permission:transaction.store')->post('/', [TransactionController::class, 'store'])->name('transaction.store');
        Route::middleware('permission:transaction.update')->put('/{transaction}', [TransactionController::class, 'update'])->name('transaction.update');
        Route::middleware('permission:transaction.delete')->delete('/{transaction}', [TransactionController::class, 'destroy'])->name('transaction.destroy');
    });

    // Purchase Orders (Pembelian) - Menggantikan 'stock-handle' untuk manajemen pembelian utama
    Route::resource('purchase_orders', PurchaseOrderController::class)
        ->middleware('permission:purchase_order.view'); // Terapkan permission ke resource controller

    // Purchase Order Items - Untuk mengelola item di dalam pesanan pembelian
    Route::resource('purchase_order_items', PurchaseOrdersItemsController::class)
        ->middleware('permission:purchase_order_item.view'); // Terapkan permission ke resource controller

    // Jenis Kendaraan
    Route::prefix('jenis-kendaraan')->group(function () {
        Route::middleware('permission:jenis-kendaraan.view')->get('/', [JenisKendaraanController::class, 'index'])->name('jenis-kendaraan.index');
        Route::middleware('permission:jenis-kendaraan.store')->post('/', [JenisKendaraanController::class, 'store'])->name('jenis-kendaraan.store');
        Route::middleware('permission:jenis-kendaraan.update')->put('/{jenis_kendaraan}', [JenisKendaraanController::class, 'update'])->name('jenis-kendaraan.update');
        Route::middleware('permission:jenis-kendaraan.delete')->delete('/{jenis_kendaraan}', [JenisKendaraanController::class, 'destroy'])->name('jenis-kendaraan.destroy');
    });

    // Laporan
    Route::prefix('laporan')->group(function () {
        Route::middleware('permission:report.transaction')->get('/transaksi', [ReportController::class, 'transactionReport'])->name('report.transaction');
        // Jika Anda menambahkan laporan pembelian di masa mendatang, bisa ditambahkan di sini:
        Route::middleware('permission:report.purchase')->get('/pembelian', [ReportController::class, 'purchaseReport'])->name('report.purchase');
    });
    Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('order.export.excel');
});

require __DIR__ . '/auth.php';

// Sparepart (resource route) - Mengubah URI menjadi plural untuk konsistensi
Route::resource('spareparts', SparepartController::class);

// Export PDF/Excel
Route::get('/supplier/export-pdf', [SupplierController::class, 'exportPDF'])->name('supplier.export-pdf');
Route::get('/customer/export-pdf', [CustomerController::class, 'exportPDF'])->name('customer.export-pdf');
Route::get('/transactions/{transaction}/invoice/pdf', [TransactionController::class, 'exportPdf'])->name('transaction.exportPdf');
Route::get('/report/transactions/export-excel', [ReportController::class, 'exportExcel'])->name('report.transaction.export.excel');
Route::get('/report/purchase/export-excel', [ReportController::class, 'exportPurchaseExcel'])->name('report.purchase.export');
Route::get('/report/exportPDF-sparepart', [ReportController::class, 'exportPdfSparepartStock'])->name('report.exportPDF-sparepart');
Route::get('/report/pergerakan-stok', [LogController::class, 'logPergerakanStok'])->name('report.pergerakan-stok');
Route::get('report/export-sparepart-report', [ReportController::class, 'exportSparepartReport'])->name('report.export-sparepart-report');
Route::get('/export-sparepart-log', [LogController::class, 'exportSparepartLog'])->name('logs.export-sparepart-log');
Route::get('/report/exportpdf-purchase', [ReportController::class, 'exportpdfPurchaseReport'])->name('report.exportpdf-purchase');
Route::get('/report/exportpdf-transaction', [ReportController::class, 'exportpdfTransactionReport'])->name('report.exportpdf-transaction');

// export import sparepart
Route::get('sparepart/export', [SparepartController::class, 'export'])->name('sparepart.export');
Route::get('sparepart/download-template', [SparepartController::class, 'downloadTemplate'])->name('sparepart.download-template');
Route::post('sparepart/import', [SparepartController::class, 'import'])->name('sparepart.import');

// direct blade route
Route::get('/report/sparepart-report', [ReportController::class, 'stockReport'])->name('report.sparepart-report');
Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');




Route::get('/inventory/report', [InventoryReportController::class, 'index'])->name('inventory.report');

Route::prefix('logs')->group(function () {
    Route::get('pembelian', [\App\Http\Controllers\LogController::class, 'logPembelian'])->name('logs.pembelian');
    Route::get('penjualan', [\App\Http\Controllers\LogController::class, 'logPenjualan'])->name('logs.penjualan');
    Route::get('stok', [\App\Http\Controllers\LogController::class, 'logPergerakanStok'])->name('logs.stok');
    Route::get('logs/sparepart', [\App\Http\Controllers\LogController::class, 'logSparepart'])->name('logs.sparepart');
    Route::get('/logs/sparepart-detail', [LogController::class, 'logSparepartDetail'])->name('logs.sparepart.detail');
    // cetak pdf route
    Route::get('logs/penjualan/pdf', [LogController::class, 'exportPdfPenjualan'])->name('logs.penjualan.pdf');
    Route::get('logs/pembelian/pdf', [LogController::class, 'exportPdfPembelian'])->name('logs.pembelian.pdf');
    Route::get('logs/stok/export', [LogController::class, 'exportExcelLogSparepart'])->name('logs.stok.export');
});

// Route::get('/purchase-orders/spareparts/{id}/latest-price', [PurchaseOrderController::class, 'getLatestPrice'])->name('purchase_orders.latest_price');
// Route::get('/api/spareparts/{id}/latest-price', [PurchaseOrderController::class, 'getLatestPrice'])->name('spareparts.latest-price');
Route::get('/get-customer/{name}', [CustomerController::class, 'getCustomer']);
Route::post('/purchase-orders/check-invoice', [PurchaseOrderController::class, 'checkInvoiceNumber'])->name('purchase_orders.checkInvoice');

Route::post('/service/{service}/change-status', [ServiceController::class, 'changeStatus'])->name('service.changeStatus');
Route::get('/service/{service}/modal-edit', [ServiceController::class, 'edit'])->name('service.modal-edit');

// additional route index service
Route::get('/service/index', [ServiceController::class, 'index'])->name('service.index');