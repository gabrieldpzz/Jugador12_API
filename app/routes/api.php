<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BannerController;

Route::get('banners', [BannerController::class, 'index']);
