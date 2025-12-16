<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalesTransactionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\UserController; // Pastikan ini di-use

/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// (Register opsional, bisa dimatikan jika tidak butuh public register)
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // REPORTS
    Route::prefix('reports')->name('reports.')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('sales');
        Route::get('/purchasement', [ReportController::class, 'purchasement'])->name('purchasement');
        Route::get('/product-sales', [ReportController::class, 'productSales'])->name('product_sales');
        Route::get('/remaining-stock', [ReportController::class, 'remainingStock'])->name('remaining_stock');
    });

    // USER MANAGEMENT (Tambahan Baru)
    Route::resource('users', UserController::class);

    // PRODUCTS
    Route::get('products/archived', [ProductController::class, 'archived'])->name('products.archived');
    Route::put('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore')->withTrashed();
    Route::resource('products', ProductController::class);

    // SUPPLIERS
    Route::get('suppliers/archived', [SupplierController::class, 'archived'])->name('suppliers.archived');
    Route::put('suppliers/{id}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore')->withTrashed();
    Route::resource('suppliers', SupplierController::class);

    // CATEGORIES
    Route::get('category_products/archived', [CategoryProductController::class, 'archived'])->name('category_products.archived');
    Route::put('category_products/{id}/restore', [CategoryProductController::class, 'restore'])->name('category_products.restore')->withTrashed();
    Route::resource('category_products', CategoryProductController::class);

    // STOCK ADJUSTMENTS
    Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show']);

    // SALES TRANSACTIONS
    Route::get('transactions/{transaction}/void-form', [SalesTransactionController::class, 'voidForm'])->name('transactions.void.form');
    Route::post('transactions/{transaction}/void', [SalesTransactionController::class, 'void'])->name('transactions.void');
    Route::get('/send-email/{id}', [SalesTransactionController::class, 'sendEmail'])->name('transactions.sendEmail');
    Route::resource('transactions', SalesTransactionController::class)->except(['destroy']);

    // PURCHASE TRANSACTIONS
    Route::get('/purchases/products-by-supplier/{supplierId}', [PurchaseController::class, 'getProductsBySupplier'])->name('purchases.products-by-supplier');
    Route::put('purchases/{purchase}/status', [PurchaseController::class, 'updateStatus'])->name('purchases.updateStatus');
    Route::get('purchases/{purchase}/void', [PurchaseController::class, 'voidForm'])->name('purchases.voidForm');
    Route::post('purchases/{purchase}/void', [PurchaseController::class, 'void'])->name('purchases.void');
    Route::resource('purchases', PurchaseController::class);

});