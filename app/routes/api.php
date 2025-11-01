<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;

// --- Público (catálogo) ---
Route::get('banners', [BannerController::class, 'index'])->name('banners.index');

Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('products/{id}/sizes', [ProductController::class, 'sizes'])->name('products.sizes');

// --- Auth + OTP (sin middleware extra; corre con el grupo 'api' por defecto) ---
Route::post('auth/send-otp',   [AuthController::class, 'sendOtp'])->name('auth.send-otp');
Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp');

// --- Ejemplo futuro: rutas protegidas por OTP verificado ---
// Route::middleware('otp.verified')->group(function () {
//     Route::post('cart/items', [CartController::class, 'store'])->name('cart.store');
//     Route::get('cart',        [CartController::class, 'index'])->name('cart.index');
// });
