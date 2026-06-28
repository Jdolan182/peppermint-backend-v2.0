<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Consumer\ConsumerAuthController;
use App\Http\Controllers\Api\UserController;

Route::get('/test', [TestController::class, 'test']);

// Admin module
Route::prefix('admin')->middleware('module:admin')->group(function () {
    Route::post('/auth/login', [AdminAuthController::class, 'login']);
    Route::post('/auth/logout', [AdminAuthController::class, 'logout'])->middleware('auth:web');

    Route::middleware('auth:web')->group(function () {
        Route::get('/user', [UserController::class, 'getUser']);
    });
});

// Consumer/public module
Route::prefix('consumer')->middleware('module:public')->group(function () {
    Route::post('/auth/login', [ConsumerAuthController::class, 'login']);
    Route::post('/auth/logout', [ConsumerAuthController::class, 'logout'])->middleware('auth:consumer');

    Route::middleware('auth:consumer')->group(function () {
        Route::get('/user', [UserController::class, 'getConsumer']);
    });
});
