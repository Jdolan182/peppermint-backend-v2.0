<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class ModulesController extends Controller
{
    private const MODULES = ['blogs', 'public', 'consumers', 'public_login', 'team', 'settings'];

    public function index(): \Illuminate\Http\JsonResponse
    {
        $result = [];

        foreach (self::MODULES as $module) {
            $envKey = 'MODULE_' . strtoupper($module) . '_ENABLED';
            $licensed = (bool) env($envKey, false);
            $result[$module] = $licensed && Setting::get('module_' . $module, 'true') !== 'false';
        }

        $result['maintenance']         = env('MAINTENANCE_MODE', false) || Setting::get('maintenance_enabled', 'false') === 'true';
        $result['maintenance_message'] = Setting::get('maintenance_message', '') ?: "We'll be back soon.";
        $result['site_name']           = Setting::get('site_name', '') ?: 'Peppermint';

        return response()->json($result);
    }
}
