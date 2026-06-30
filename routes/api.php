<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\UsersController;
use App\Http\Controllers\Api\Admin\ConsumersController;
use App\Http\Controllers\Api\Admin\BlogsController;
use App\Http\Controllers\Api\Admin\CategoriesController;
use App\Http\Controllers\Api\Admin\SettingsController;
use App\Http\Controllers\Api\Consumer\ConsumerAuthController;
use App\Http\Controllers\Api\Public\BlogsController as PublicBlogsController;
use App\Http\Controllers\Api\Public\ModulesController as PublicModulesController;
use App\Http\Controllers\Api\UserController;

Route::get('/test', [TestController::class, 'test']);

// Admin module
Route::prefix('admin')->middleware(['module:admin', 'maintenance:admin'])->group(function () {
    Route::post('/auth/login', [AdminAuthController::class, 'login']);
    Route::post('/auth/logout', [AdminAuthController::class, 'logout'])->middleware('auth:web');

    Route::middleware('auth:web')->group(function () {
        Route::get('/user', [UserController::class, 'getUser']);

        Route::middleware('module:consumers')->group(function () {
            Route::apiResource('consumers', ConsumersController::class);
        });

        Route::middleware('module:team')->group(function () {
            Route::apiResource('users', UsersController::class);
        });

        Route::middleware('module:blogs')->group(function () {
            Route::apiResource('blogs', BlogsController::class);
            Route::apiResource('categories', CategoriesController::class);
        });

        Route::middleware('module:settings')->group(function () {
            Route::get('settings', [SettingsController::class, 'index']);
            Route::put('settings', [SettingsController::class, 'update']);
        });
    });
});

// Public content (no auth)
Route::prefix('public')->group(function () {
    // /modules is never blocked — frontend needs it to know maintenance state
    Route::get('/modules', [PublicModulesController::class, 'index']);

    Route::middleware(['maintenance', 'module:blogs'])->group(function () {
        Route::get('/blogs', [PublicBlogsController::class, 'index']);
        Route::get('/blogs/{slug}', [PublicBlogsController::class, 'show']);
    });
});

// Consumer module
Route::prefix('consumer')->middleware(['module:consumers', 'maintenance'])->group(function () {
    Route::post('/auth/login', [ConsumerAuthController::class, 'login'])->middleware('module:public_login');
    Route::post('/auth/logout', [ConsumerAuthController::class, 'logout'])->middleware('auth:consumer');

    Route::middleware('auth:consumer')->group(function () {
        Route::get('/user', [UserController::class, 'getConsumer']);
    });
});
