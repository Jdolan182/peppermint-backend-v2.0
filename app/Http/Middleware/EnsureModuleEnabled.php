<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Env check — the licence gate (set by the developer at deployment)
        $envKey = 'MODULE_' . strtoupper($module) . '_ENABLED';
        if (!env($envKey, false)) {
            return response()->json(['message' => 'Module not available'], 404);
        }

        // DB check — the on/off toggle (set by the admin at runtime)
        // Defaults to enabled if no setting has been saved yet
        $dbKey = 'module_' . strtolower($module);
        if (Setting::get($dbKey, 'true') === 'false') {
            return response()->json(['message' => 'Module not available'], 404);
        }

        return $next($request);
    }
}
