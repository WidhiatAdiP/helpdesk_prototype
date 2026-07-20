<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;


class ActivityLogger
{

    public static function log(
        $ticketId,
        $action,
        $description
    )
    {

        ActivityLog::create([

            'ticket_id' => $ticketId,

            'user_id' => Auth::id(),

            'action' => $action,

            'description' => $description,

        ]);

    }

}