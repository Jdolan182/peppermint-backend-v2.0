<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SpaController;

Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);

// Catch-all: serves the built SPA with meta tags injected server-side.
// API, storage, and the health endpoint are excluded.
Route::get('/{any?}', SpaController::class)
    ->where('any', '^(?!api(/|$)|storage(/|$)|up$).*');
