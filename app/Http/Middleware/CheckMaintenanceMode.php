<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next, string $scope = 'public'): Response
    {
        // Env-based: blocks everything including admin (set at deploy time)
        if (config('peppermint.maintenance_mode', false)) {
            return response()->json([
                'message' => 'Service temporarily unavailable',
                'maintenance' => true,
            ], 503);
        }

        // DB-based: blocks public/consumer routes only (admin stays up)
        if ($scope !== 'admin' && Setting::get('maintenance_enabled', 'false') === 'true') {
            return response()->json([
                'message' => 'Service temporarily unavailable',
                'maintenance' => true,
            ], 503);
        }

        return $next($request);
    }
}
