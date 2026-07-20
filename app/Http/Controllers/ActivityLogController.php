<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->isAgent(),
            403
        );


        $logs = ActivityLog::with([
                'user',
                'ticket'
            ])
            ->latest()
            ->paginate(5)
            ->through(function ($log) {

                $log->time =
                    $log->created_at
                    ->format('d-m-Y H:i');


                return $log;

            });


        return Inertia::render(
            'Logs/Activity',
            [
                'logs'=>$logs
            ]
        );
    }
}