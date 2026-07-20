<?php

namespace App\Helpers;

use App\Models\SystemLog;

class SystemLogger
{
    public static function log(
        $action,
        $description
    ) {

        SystemLog::create([

            'user_id' => auth()->id(),

            'action' => $action,

            'description' => $description,

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

        ]);

    }
}