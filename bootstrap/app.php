<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // TLS terminates at a reverse proxy in front of the containers;
        // trust its forwarded headers so scheme/host resolve correctly.
        // The app container is only reachable from the internal network.
        $middleware->trustProxies(at: '*');
        $middleware->statefulApi();
        $middleware->api(append: [
            \Illuminate\Session\Middleware\StartSession::class,
        ]);
        $middleware->alias([
            'module'      => \App\Http\Middleware\EnsureModuleEnabled::class,
            'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
