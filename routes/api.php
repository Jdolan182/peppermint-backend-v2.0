<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminPasswordController;
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
use App\Http\Controllers\Api\Consumer\ConsumerPasswordController;
use App\Http\Controllers\Api\Public\BlogsController as PublicBlogsController;
use App\Http\Controllers\Api\Public\ModulesController as PublicModulesController;
use App\Http\Controllers\Api\Public\PagesController as PublicPagesController;
use App\Http\Controllers\Api\Public\FooterController as PublicFooterController;
use App\Http\Controllers\Api\Public\ContactController as PublicContactController;
use App\Http\Controllers\Api\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Api\Admin\TaskTypesController;
use App\Http\Controllers\Api\Admin\TaskStatusesController;
use App\Http\Controllers\Api\Admin\TasksController;
use App\Http\Controllers\Api\Admin\RoadmapController as AdminRoadmapController;
use App\Http\Controllers\Api\Admin\RoadmapCategoriesController;
use App\Http\Controllers\Api\Admin\CalendarController;
use App\Http\Controllers\Api\Admin\StatsController;
use App\Http\Controllers\Api\Consumer\TasksController as ConsumerTasksController;
use App\Http\Controllers\Api\Public\RoadmapController as PublicRoadmapController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\UserController;

Route::post('/maintenance/unlock', [MaintenanceController::class, 'unlock'])->middleware('throttle:5,1');

// Admin module
Route::prefix('admin')->middleware(['module:admin', 'maintenance:admin'])->group(function () {
    Route::post('/auth/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/auth/logout', [AdminAuthController::class, 'logout'])->middleware('auth:web');
    Route::post('/auth/forgot-password', [AdminPasswordController::class, 'forgot'])->middleware('throttle:5,1');
    Route::post('/auth/reset-password', [AdminPasswordController::class, 'reset']);

    Route::middleware('auth:web')->group(function () {
        Route::get('/user', [UserController::class, 'getUser']);
        Route::put('/user', [UserController::class, 'updateAdmin']);

        Route::middleware('module:consumers')->group(function () {
            Route::apiResource('consumers', ConsumersController::class)->except(['show']);
        });

        Route::middleware('module:team')->group(function () {
            Route::apiResource('users', UsersController::class)->except(['show']);
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

        Route::middleware('module:tasks')->group(function () {
            Route::get('task-types', [TaskTypesController::class, 'index']);
            Route::post('task-types', [TaskTypesController::class, 'store']);
            Route::put('task-types/{taskType}', [TaskTypesController::class, 'update']);
            Route::delete('task-types/{taskType}', [TaskTypesController::class, 'destroy']);

            Route::get('task-statuses', [TaskStatusesController::class, 'index']);
            Route::post('task-statuses', [TaskStatusesController::class, 'store']);
            Route::put('task-statuses/{taskStatus}', [TaskStatusesController::class, 'update']);
            Route::delete('task-statuses/{taskStatus}', [TaskStatusesController::class, 'destroy']);

            Route::get('tasks', [TasksController::class, 'index']);
            Route::post('tasks', [TasksController::class, 'store']);
            Route::get('tasks/{task}', [TasksController::class, 'show']);
            Route::put('tasks/{task}', [TasksController::class, 'update']);
            Route::patch('tasks/{task}', [TasksController::class, 'update']);
            Route::delete('tasks/{task}', [TasksController::class, 'destroy']);
        });

        Route::middleware('module:roadmap')->group(function () {
            Route::get('roadmap', [AdminRoadmapController::class, 'index']);
            Route::post('roadmap', [AdminRoadmapController::class, 'store']);
            Route::get('roadmap/{roadmapItem}', [AdminRoadmapController::class, 'show']);
            Route::put('roadmap/{roadmapItem}', [AdminRoadmapController::class, 'update']);
            Route::delete('roadmap/{roadmapItem}', [AdminRoadmapController::class, 'destroy']);
            Route::put('roadmap-order', [AdminRoadmapController::class, 'saveOrder']);
            Route::get('roadmap-categories', [RoadmapCategoriesController::class, 'index']);
            Route::post('roadmap-categories', [RoadmapCategoriesController::class, 'store']);
            Route::put('roadmap-categories/{roadmapCategory}', [RoadmapCategoriesController::class, 'update']);
            Route::delete('roadmap-categories/{roadmapCategory}', [RoadmapCategoriesController::class, 'destroy']);
        });

        // Stats — auth-protected; frontend gates visibility based on modules
        Route::get('stats', [StatsController::class, 'index']);

        // Calendar — auth-protected; frontend gates visibility based on tasks/roadmap modules
        Route::get('calendar', [CalendarController::class, 'index']);
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
        Route::post('/contact', [PublicContactController::class, 'store'])->middleware('throttle:10,1');
    });
});

// Consumer module
Route::prefix('consumer')->middleware(['module:consumers', 'maintenance'])->group(function () {
    Route::post('/auth/login', [ConsumerAuthController::class, 'login'])->middleware(['module:public_login', 'throttle:10,1']);
    Route::post('/auth/logout', [ConsumerAuthController::class, 'logout'])->middleware('auth:consumer');
    Route::post('/auth/forgot-password', [ConsumerPasswordController::class, 'forgot'])->middleware('throttle:5,1');
    Route::post('/auth/reset-password', [ConsumerPasswordController::class, 'reset']);

    Route::middleware('auth:consumer')->group(function () {
        Route::get('/user', [UserController::class, 'getConsumer']);
        Route::put('/user', [UserController::class, 'updateConsumer']);

        Route::middleware('module:tasks_consumer')->group(function () {
            Route::get('/tasks', [ConsumerTasksController::class, 'index']);
            Route::get('/tasks/{task}', [ConsumerTasksController::class, 'show']);
        });
    });
});

// Public roadmap
Route::prefix('public')->middleware(['maintenance', 'module:roadmap_public'])->group(function () {
    Route::get('/roadmap', [PublicRoadmapController::class, 'index']);
});
