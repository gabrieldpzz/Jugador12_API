<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ProductController;

Route::get('banners', [BannerController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('products/{id}/sizes', [ProductController::class, 'sizes']);
