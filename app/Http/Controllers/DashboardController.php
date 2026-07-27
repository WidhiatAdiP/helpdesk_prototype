<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Ticket::query();

        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        // Stats dasar
        $stats = [
            'total'       => (clone $query)->count(),
            'open'        => (clone $query)->where('status', 'open')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'resolved'    => (clone $query)->where('status', 'resolved')->count(),
            'closed'      => (clone $query)->where('status', 'closed')->count(),
        ];

        // Priority breakdown
        $priority = [
            'low'    => (clone $query)->where('priority', 'low')->count(),
            'medium' => (clone $query)->where('priority', 'medium')->count(),
            'high'   => (clone $query)->where('priority', 'high')->count(),
            'urgent' => (clone $query)->where('priority', 'urgent')->count(),
        ];

        // 5 tiket terbaru
        $recentTickets = (clone $query)
            ->with(['user:id,name', 'assignee:id,name'])
            ->latest()
            ->limit(5)
            ->get(['id', 'title', 'status', 'priority', 'user_id', 'assignee_id', 'created_at']);

        // Unassigned tickets (hanya untuk admin & agent)
        $unassignedCount = null;
        if (in_array($user->role, ['admin', 'agent'])) {
            $unassignedCount = Ticket::whereNull('assignee_id')
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count();
        }

        return Inertia::render('Dashboard', [
            'stats'          => $stats,
            'priority'       => $priority,
            'recentTickets'  => $recentTickets,
            'unassignedCount' => $unassignedCount,
        ]);
    }
}