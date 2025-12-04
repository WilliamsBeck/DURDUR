<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;


// === Redirect root ke halaman login ===
Route::get('/', function () {
    return redirect()->route('login');
});

// === ROUTE AUTHENTICATION ===
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/send-email/{to}/{id}',[\App\Http\Controllers\SalesTransactionController::class,'sendEmail']);


Route::middleware(['auth'])->group(function () {

    //route resource for dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //route resource for products
    Route::resource('/products', \App\Http\Controllers\ProductController::class);

    //route resource for suppliers
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

    //route resource for product category
    Route::resource('/category_products', \App\Http\Controllers\CategoryProductController::class);

    //route resource for sales transactions

    Route::resource('/transactions', \App\Http\Controllers\SalesTransactionController::class);

    

});
