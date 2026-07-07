<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class ModulesController extends Controller
{
    private const MODULES = ['blogs', 'public', 'consumers', 'public_login', 'team', 'settings', 'pages', 'tasks', 'tasks_consumer', 'roadmap', 'roadmap_public'];

    public function index(): \Illuminate\Http\JsonResponse
    {
        $result = [];

        foreach (self::MODULES as $module) {
            $licensed = config('peppermint.modules.' . $module, false);
            $result[$module] = $licensed && Setting::get('module_' . $module, 'true') !== 'false';
        }

        $result['maintenance']         = config('peppermint.maintenance_mode', false) || Setting::get('maintenance_enabled', 'false') === 'true';
        $result['maintenance_message'] = Setting::get('maintenance_message', '') ?: "We'll be back soon.";
        $result['site_name']           = Setting::get('site_name', '') ?: 'Peppermint';
        $result['consumer_label']      = Setting::get('consumer_label', '') ?: 'Consumer';

        return response()->json($result);
    }
}
