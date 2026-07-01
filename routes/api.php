<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\UsersController;
use App\Http\Controllers\Api\Admin\ConsumersController;
use App\Http\Controllers\Api\Admin\BlogsController;
use App\Http\Controllers\Api\Admin\CategoriesController;
use App\Http\Controllers\Api\Admin\SettingsController;
use App\Http\Controllers\Api\Admin\PagesController;
use App\Http\Controllers\Api\Admin\SectionsController;
use App\Http\Controllers\Api\Admin\FooterController;
use App\Http\Controllers\Api\Admin\MediaController;
use App\Http\Controllers\Api\Consumer\ConsumerAuthController;
use App\Http\Controllers\Api\Public\BlogsController as PublicBlogsController;
use App\Http\Controllers\Api\Public\ModulesController as PublicModulesController;
use App\Http\Controllers\Api\Public\PagesController as PublicPagesController;
use App\Http\Controllers\Api\Public\FooterController as PublicFooterController;
use App\Http\Controllers\Api\Public\ContactController as PublicContactController;
use App\Http\Controllers\Api\Admin\ContactController as AdminContactController;
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

        Route::middleware('module:pages')->group(function () {
            // Pages — static routes before parameterized to avoid binding conflicts
            Route::get('pages', [PagesController::class, 'index']);
            Route::post('pages', [PagesController::class, 'store']);
            Route::put('pages/nav-order', [PagesController::class, 'saveNavOrder']);
            Route::get('pages/{page}/preview', [PagesController::class, 'preview']);
            Route::get('pages/{page}', [PagesController::class, 'show']);
            Route::put('pages/{page}', [PagesController::class, 'update']);
            Route::delete('pages/{page}', [PagesController::class, 'destroy']);
            Route::post('pages/{page}/home', [PagesController::class, 'setHome']);
            Route::post('pages/{page}/sections', [SectionsController::class, 'store']);

            // Sections
            Route::put('sections/order', [SectionsController::class, 'saveOrder']);
            Route::put('sections/{section}', [SectionsController::class, 'update']);
            Route::delete('sections/{section}', [SectionsController::class, 'destroy']);

            // Footer
            Route::get('footer', [FooterController::class, 'index']);
            Route::post('footer/sections', [FooterController::class, 'store']);
            Route::put('footer/sections/order', [FooterController::class, 'saveOrder']);
            Route::put('footer/sections/{footerSection}', [FooterController::class, 'update']);
            Route::delete('footer/sections/{footerSection}', [FooterController::class, 'destroy']);

            // Media
            Route::get('media', [MediaController::class, 'index']);
            Route::post('media', [MediaController::class, 'store']);
            Route::put('media/{media}', [MediaController::class, 'update']);
            Route::delete('media/{media}', [MediaController::class, 'destroy']);

            // Contact submissions — static routes before parameterized
            Route::get('contact', [AdminContactController::class, 'index']);
            Route::get('contact/unread-count', [AdminContactController::class, 'unreadCount']);
            Route::post('contact/{submission}/read', [AdminContactController::class, 'markRead']);
            Route::delete('contact/{submission}', [AdminContactController::class, 'destroy']);
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

    Route::middleware(['maintenance', 'module:pages'])->group(function () {
        Route::get('/pages/nav', [PublicPagesController::class, 'nav']);
        Route::get('/pages/home', [PublicPagesController::class, 'home']);
        Route::get('/pages/{slug}', [PublicPagesController::class, 'show']);
        Route::get('/footer', [PublicFooterController::class, 'index']);
        Route::post('/contact', [PublicContactController::class, 'store']);
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
