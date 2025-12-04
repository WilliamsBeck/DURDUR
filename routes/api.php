<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesTransactionController;
use App\Models\SalesTransaction;

Route::prefix('products')->group(function () {
    Route::get('/lihat', [ProductController::class, 'lihat']);
    Route::get('/lihat/{id}', [ProductController::class, 'lihat_id']);
    Route::post('/tambah', [ProductController::class, 'store_api']);
    Route::put('/update/{id}', [ProductController::class, 'update_api']);
    Route::delete('/hapus/{id}', [ProductController::class, 'delete_api']);
});

Route::prefix('sales')->group(function () {
    Route::get('/lihat', [SalesTransactionController::class, 'lihat']);
    Route::get('/lihat/{id}', [SalesTransactionController::class, 'lihat_id']);
});


Route::apiResource('users', UserController::class);
Route::post('login', [UserController::class, 'login']);

Route::get('test',function () {
    return response()->json(['message'=>'API is working!']);
});


