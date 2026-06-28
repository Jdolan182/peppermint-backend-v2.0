<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $key = 'MODULE_' . strtoupper($module) . '_ENABLED';

        if (!env($key, false)) {
            return response()->json(['message' => 'Module not available'], 404);
        }

        return $next($request);
    }
}
