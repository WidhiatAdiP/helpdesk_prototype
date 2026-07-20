<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Inertia\Inertia;

class LogController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        $perPage = request('perPage', 10);

        $logs = ActivityLog::with([
            'user',
            'ticket',
        ])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Logs/Index', [
            'logs' => $logs,
        ]);
    }
}