<?php

namespace App\Support;

use App\Models\Setting;

class Modules
{
    /**
     * Same two-gate check as the EnsureModuleEnabled middleware:
     * env licence gate AND runtime DB toggle (default enabled).
     */
    public static function enabled(string $module): bool
    {
        if (!config('peppermint.modules.' . $module, false)) {
            return false;
        }

        return Setting::get('module_' . strtolower($module), 'true') !== 'false';
    }
}
