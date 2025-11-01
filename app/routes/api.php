<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;

Route::get('banners', [BannerController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('products/{id}/sizes', [ProductController::class, 'sizes']);

// Requiere Firebase ID Token
Route::middleware('firebase.auth')->group(function () {
    Route::post('auth/send-otp',   [AuthController::class, 'sendOtp']);
    Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);

    // Ejemplo de carrito protegido doblemente:
    Route::middleware('otp.verified')->group(function () {
        // Route::post('cart/items', [CartController::class, 'store']);
        // Route::get('cart',        [CartController::class, 'index']);
    });
});