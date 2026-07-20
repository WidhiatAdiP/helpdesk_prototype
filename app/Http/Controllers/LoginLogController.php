<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Inertia\Inertia;

class LoginLogController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->isAgent(),
            403
        );

        $perPage = request('perPage', 5);

        $logs = LoginLog::with('user')
            ->latest('login_at')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($log) {
                $log->status = $log->status === 'login'
                    ? 'Logged In'
                    : 'Logged Out';

                $log->status_time = $log->login_at->format('d-m-Y H:i:s');

                return $log;

            });

        return Inertia::render('Logs/Login', [
            'logs' => $logs,
        ]);
    }
}