<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalesTransactionController;
use App\Http\Controllers\PurchaseController;


/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| EMAIL (OPTIONAL / TESTING)
|--------------------------------------------------------------------------
*/
Route::get('/send-email/{id}', [SalesTransactionController::class, 'sendEmail'])
    ->name('transactions.sendEmail');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ================= DASHBOARD =================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ================= PRODUCTS =================
    Route::resource('/products', \App\Http\Controllers\ProductController::class);

    // ================= SUPPLIERS =================
    Route::resource('/suppliers', \App\Http\Controllers\SupplierController::class);

    // ================= PRODUCT CATEGORIES =================
    Route::resource('/category_products', \App\Http\Controllers\CategoryProductController::class);

    // ================= SALES TRANSACTIONS =================
    // ❌ DESTROY DIHAPUS (KARENA PAKAI VOID)
    Route::resource('/transactions', SalesTransactionController::class)
        ->except(['destroy']);


   // Route baru untuk menampilkan form void
    Route::get('transactions/{transaction}/void-form', [SalesTransactionController::class, 'voidForm'])
        ->name('transactions.void.form');

// Route untuk memproses POST/submit void (Ini sudah ada dari jawaban sebelumnya, pastikan tetap ada)
    Route::post('transactions/{transaction}/void', [SalesTransactionController::class, 'void'])
        ->name('transactions.void');







   // ... (Routes untuk Sales Transactions) ...



// Routes untuk Purchase Transactions
Route::resource('purchases', PurchaseController::class)->except(['edit', 'update', 'destroy']);

Route::get('/purchases/products-by-supplier/{supplierId}', [PurchaseController::class, 'getProductsBySupplier'])->name('purchases.products-by-supplier');


// routes/web.php

Route::resource('purchases', PurchaseController::class);

// Tambahkan route ini di luar resource
Route::put('purchases/{purchase}/status', [PurchaseController::class, 'updateStatus'])
     ->name('purchases.updateStatus');

     // Route untuk menampilkan form void
    Route::get('purchases/{purchase}/void', [PurchaseController::class, 'voidForm'])
         ->name('purchases.voidForm');

    // Route untuk memproses void (menggunakan POST)
    Route::post('purchases/{purchase}/void', [PurchaseController::class, 'void'])
         ->name('purchases.void');
});
