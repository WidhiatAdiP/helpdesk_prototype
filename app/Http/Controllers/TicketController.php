<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Comment;
use App\Models\User;
use App\Models\Attachment;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();
        if(auth()->user()->role === 'user'){
            $query->where(
                'user_id',
                auth()->id()
            );
        }

        $tickets = $query
            ->with(['user', 'assignee'])
            ->withCount(['comments', 'attachments'])
            ->when($request->search,function($query,$search){
                $query->where(
                    'title',
                    'like',
                    "%{$search}%"
                );
            })
            ->when($request->status,function($query,$status){
                $query->where(
                    'status',
                    $status
                );
            })
            ->when($request->category,function($query,$category){
                $query->where(
                    'category',
                    $category
                );
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Hitung resolution time & SLA untuk setiap ticket yang sudah resolved
        $tickets->getCollection()->transform(function ($ticket) {
            $ticket->resolution = $this->calculateResolution($ticket);
            return $ticket;
        });

        return Inertia::render(
            'Tickets/Index',
            [
                'tickets'=>$tickets,
                'filters'=>[
                    'search'=>$request->search,
                    'status'=>$request->status,
                    'category'=>$request->category,
                ],
            ]
        );
    }

    public function report(Request $request)
    {
        if (
            !auth()->user()->isAdmin() &&
            !auth()->user()->isAgent()
        ) {
            abort(403);
        }

        $today = now()->endOfDay();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : $today;

        // Batasi end_date tidak boleh melebihi hari ini
        if ($endDate->gt($today)) {
            $endDate = $today;
        }

        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : $endDate->copy()->subDays(1)->startOfDay();

        // Batasi start_date tidak boleh melebihi end_date
        if ($startDate->gt($endDate)) {
            $startDate = $endDate->copy()->startOfDay();
        }

        // Ticket yang dibuat dalam rentang tanggal
        $createdTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->get(['id', 'created_at', 'status', 'priority', 'category']);

        // Ticket yang resolved dalam rentang tanggal
        $resolvedTickets = Ticket::whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->get(['id', 'created_at', 'resolved_at', 'priority']);

        // Siapkan slot tanggal kosong dari start sampai end
        $days = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $key = $cursor->format('Y-m-d');
            $days[$key] = [
                'date' => $key,
                'label' => $cursor->translatedFormat('d M'),
                'created_count' => 0,
                'resolved_count' => 0,
                'sla_met_count' => 0,
                'sla_breach_count' => 0,
            ];
            $cursor->addDay();
        }

        // Hitung ticket dibuat per hari
        foreach ($createdTickets as $ticket) {
            $key = $ticket->created_at->format('Y-m-d');
            if (isset($days[$key])) {
                $days[$key]['created_count']++;
            }
        }

        // Hitung ticket resolved per hari + status SLA
        foreach ($resolvedTickets as $ticket) {
            $key = $ticket->resolved_at->format('Y-m-d');
            if (!isset($days[$key])) {
                continue;
            }

            $days[$key]['resolved_count']++;

            $diffMinutes = $ticket->created_at->diffInMinutes($ticket->resolved_at);
            $slaHours = $this->getSlaHours($ticket->priority);

            if ($diffMinutes <= ($slaHours * 60)) {
                $days[$key]['sla_met_count']++;
            } else {
                $days[$key]['sla_breach_count']++;
            }
        }

        $daily = array_values($days);

        // Ringkasan status ticket yang dibuat dalam rentang tanggal
        $statusSummary = [
            'open' => 0,
            'in_progress' => 0,
            'resolved' => 0,
            'closed' => 0,
        ];
        foreach ($createdTickets as $ticket) {
            if (isset($statusSummary[$ticket->status])) {
                $statusSummary[$ticket->status]++;
            }
        }

        // Ringkasan priority ticket yang dibuat dalam rentang tanggal
        $prioritySummary = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'urgent' => 0,
        ];
        foreach ($createdTickets as $ticket) {
            if (isset($prioritySummary[$ticket->priority])) {
                $prioritySummary[$ticket->priority]++;
            }
        }

        $totalCreated = $createdTickets->count();
        $totalResolved = $resolvedTickets->count();
        $totalSlaMet = collect($daily)->sum('sla_met_count');
        $totalSlaBreach = collect($daily)->sum('sla_breach_count');

        $slaComplianceRate = $totalResolved > 0
            ? round(($totalSlaMet / $totalResolved) * 100, 1)
            : null;

        $avgResolutionMinutes = $resolvedTickets->count() > 0
            ? $resolvedTickets->avg(
                fn ($t) => $t->created_at->diffInMinutes($t->resolved_at)
            )
            : 0;

        $avgHours = intdiv((int) $avgResolutionMinutes, 60);
        $avgMinutes = (int) $avgResolutionMinutes % 60;

        return Inertia::render('Reports/Index', [
            'daily' => $daily,
            'summary' => [
                'total_created' => $totalCreated,
                'total_resolved' => $totalResolved,
                'total_sla_met' => $totalSlaMet,
                'total_sla_breach' => $totalSlaBreach,
                'sla_compliance_rate' => $slaComplianceRate,
                'avg_resolution_text' => "{$avgHours}h {$avgMinutes}m",
            ],
            'status_summary' => $statusSummary,
            'priority_summary' => $prioritySummary,
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
        ]);
    }

    public function reportOverview()
    {
        if (
            !auth()->user()->isAdmin() &&
            !auth()->user()->isAgent()
        ) {
            abort(403);
        }

        $totalCreated = Ticket::count();
        $totalResolved = Ticket::whereNotNull('resolved_at')->count();

        // Status summary — aggregate query, tidak load semua baris
        $statusSummary = [
            'open' => 0,
            'in_progress' => 0,
            'resolved' => 0,
            'closed' => 0,
        ];
        Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->each(function ($count, $status) use (&$statusSummary) {
                if (isset($statusSummary[$status])) {
                    $statusSummary[$status] = $count;
                }
            });

        // Priority summary — aggregate query
        $prioritySummary = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'urgent' => 0,
        ];
        Ticket::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->each(function ($count, $priority) use (&$prioritySummary) {
                if (isset($prioritySummary[$priority])) {
                    $prioritySummary[$priority] = $count;
                }
            });

        // SLA & rata-rata resolusi — chunk hanya ticket resolved, memory-safe
        $slaMet = 0;
        $slaBreach = 0;
        $totalResolutionMinutes = 0;
        $resolutionSampleCount = 0;

        Ticket::whereNotNull('resolved_at')
            ->select('id', 'created_at', 'resolved_at', 'priority')
            ->chunk(500, function ($tickets) use (
                &$slaMet,
                &$slaBreach,
                &$totalResolutionMinutes,
                &$resolutionSampleCount
            ) {
                foreach ($tickets as $ticket) {
                    $diffMinutes = $ticket->created_at->diffInMinutes($ticket->resolved_at);
                    $slaHours = $this->getSlaHours($ticket->priority);

                    if ($diffMinutes <= ($slaHours * 60)) {
                        $slaMet++;
                    } else {
                        $slaBreach++;
                    }

                    $totalResolutionMinutes += $diffMinutes;
                    $resolutionSampleCount++;
                }
            });

        $slaComplianceRate = $totalResolved > 0
            ? round(($slaMet / $totalResolved) * 100, 1)
            : null;

        $avgResolutionMinutes = $resolutionSampleCount > 0
            ? $totalResolutionMinutes / $resolutionSampleCount
            : 0;

        $avgHours = intdiv((int) $avgResolutionMinutes, 60);
        $avgMinutes = (int) $avgResolutionMinutes % 60;

        // Monthly breakdown — 12 bulan terakhir, pakai aggregate query
        $rangeStart = now()->startOfMonth()->subMonths(11);
        $endCursor = now()->endOfMonth();

        $months = [];
        $cursor = $rangeStart->copy();
        while ($cursor->lte($endCursor)) {
            $key = $cursor->format('Y-m');
            $months[$key] = [
                'month' => $key,
                'label' => $cursor->translatedFormat('M Y'),
                'created_count' => 0,
                'resolved_count' => 0,
            ];
            $cursor->addMonth();
        }

        // Catatan: DATE_FORMAT adalah fungsi MySQL/MariaDB.
        // Jika pakai database lain (SQLite/PostgreSQL), sesuaikan fungsinya.
        $createdMonthly = Ticket::where('created_at', '>=', $rangeStart)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, count(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        foreach ($createdMonthly as $key => $count) {
            if (isset($months[$key])) {
                $months[$key]['created_count'] = $count;
            }
        }

        $resolvedMonthly = Ticket::whereNotNull('resolved_at')
            ->where('resolved_at', '>=', $rangeStart)
            ->selectRaw("DATE_FORMAT(resolved_at, '%Y-%m') as ym, count(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        foreach ($resolvedMonthly as $key => $count) {
            if (isset($months[$key])) {
                $months[$key]['resolved_count'] = $count;
            }
        }

        $firstTicketDate = Ticket::min('created_at');

        return Inertia::render('Reports/Overview', [
            'monthly' => array_values($months),
            'summary' => [
                'total_created' => $totalCreated,
                'total_resolved' => $totalResolved,
                'total_sla_met' => $slaMet,
                'total_sla_breach' => $slaBreach,
                'sla_compliance_rate' => $slaComplianceRate,
                'avg_resolution_text' => "{$avgHours}h {$avgMinutes}m",
            ],
            'status_summary' => $statusSummary,
            'priority_summary' => $prioritySummary,
            'first_ticket_date' => $firstTicketDate
                ? Carbon::parse($firstTicketDate)->translatedFormat('d M Y')
                : null,
        ]);
    }

    private function calculateResolution(Ticket $ticket)
    {
        if (!$ticket->resolved_at) {
            return null;
        }

        $created = $ticket->created_at;
        $resolved = $ticket->resolved_at;

        $diffMinutes = $created->diffInMinutes($resolved);

        $hours = intdiv($diffMinutes, 60);
        $minutes = $diffMinutes % 60;

        $slaHours = $this->getSlaHours($ticket->priority);

        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'text' => "{$hours} hour" . ($hours != 1 ? "s" : "") .
                " {$minutes} minute" . ($minutes != 1 ? "s" : ""),
            'sla_hours' => $slaHours,
            'within_sla' => $diffMinutes <= ($slaHours * 60),
        ];
    }

    private function getSlaHours(string $priority): int
    {
        return match ($priority) {
            'low' => 72,
            'medium' => 24,
            'high' => 8,
            'urgent' => 4,
            default => 24,
        };
    }

    public function create()
    {
        if(!auth()->user()->isUser()){
            abort(403);
        }
        return Inertia::render(
            'Tickets/Create'
        );
    }

    public function show(Ticket $ticket)
    {
        if (
            auth()->user()->role === 'user'
            &&
            $ticket->user_id !== auth()->id()
        ) {
            abort(403);
        }

        $ticket->load([
            'user',
            'assignee',
            'comments.user',
            'activityLogs.user',
            'attachments',
        ]);

        // ===============================
        // Hitung Resolution Time & SLA
        // ===============================
        $resolution = $this->calculateResolution($ticket);

        if ($ticket->resolved_at) {

            $created = $ticket->created_at;
            $resolved = $ticket->resolved_at;

            $diffMinutes = $created->diffInMinutes($resolved);

            $hours = intdiv($diffMinutes, 60);
            $minutes = $diffMinutes % 60;

            // SLA berdasarkan priority
            $slaHours = match ($ticket->priority) {
                'low' => 72,
                'medium' => 24,
                'high' => 8,
                'urgent' => 4,
                default => 24,
            };

            $resolution = [
                'hours' => $hours,
                'minutes' => $minutes,
                'text' => "{$hours} hour" . ($hours != 1 ? "s" : "") .
                    " {$minutes} minute" . ($minutes != 1 ? "s" : ""),
                'sla_hours' => $slaHours,
                'within_sla' => $diffMinutes <= ($slaHours * 60),
            ];
        }

        return Inertia::render(
            'Tickets/Show',
            [
                'ticket' => $ticket,
                'resolution' => $resolution,

                'users' => User::whereIn(
                    'role',
                    [
                        'admin',
                        'agent',
                    ]
                )
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                ]),
            ]
        );
    }

    public function store(Request $request)
    {
        if(!auth()->user()->isUser()){
            abort(403);
        }

        $validated=$request->validate([
            'title'=>[
                'required',
                'max:255'
            ],
            'description'=>[
                'required'
            ],
            'priority'=>[
                'required'
            ],
            'category'=>[
                'nullable'
            ],
            'attachment'=>[
                'nullable',
                'file',
                'max:5120'
            ],
        ]);

        $ticket = Ticket::create([
            'user_id'=>auth()->id(),
            'title'=>$validated['title'],
            'description'=>$validated['description'],
            'priority'=>$validated['priority'],
            'category'=>$validated['category'] ?? null,
            'status'=>'open',
        ]);

        if($request->hasFile('attachment')){
            $path=$request
                ->file('attachment')
                ->store(
                    'attachments',
                    'public'
                );

            Attachment::create([
                'ticket_id'=>$ticket->id,
                'user_id'=>auth()->id(),
                'filename'=>$request
                    ->file('attachment')
                    ->getClientOriginalName(),
                'path'=>$path,
            ]);
        }

        ActivityLogger::log(
            $ticket->id,
            'created',
            'Created ticket: '.$ticket->title
        );

        return redirect()
            ->route('tickets.index')
            ->with(
                'success',
                'Ticket berhasil dibuat.'
            );
    }

    public function storeComment(
        Request $request,
        Ticket $ticket
    )
    {
        $request->validate([
            'comment' => [
                'nullable',
                'required_without:image'
            ],
            'image' => [
                'nullable',
                'image',
                'max:5120'
            ],
        ]);

        // Handle upload gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request
                ->file('image')
                ->store('comment-images', 'public');
        }

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'image_path' => $imagePath,
        ]);

        ActivityLogger::log(
            $ticket->id,
            'comment_added',
            'Added a comment'
        );

        return back()
            ->with(
                'success',
                'Komentar berhasil ditambahkan.'
            );
    }

    public function updateStatus(
        Request $request,
        Ticket $ticket
    )
    {
        if (
            !auth()->user()->isAdmin() &&
            !auth()->user()->isAgent()
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'in:open,in_progress,resolved,closed',
            ],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $ticket->status;

        /*
        |--------------------------------------------------------------------------
        | CLOSED = FINAL
        |--------------------------------------------------------------------------
        */
        if ($oldStatus === 'closed') {
            return back()->with(
                'error',
                'Closed ticket tidak dapat diubah lagi.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RESOLVED = hanya boleh menjadi CLOSED oleh ADMIN
        |--------------------------------------------------------------------------
        */
        if ($oldStatus === 'resolved') {

            if (
                !auth()->user()->isAdmin() ||
                $newStatus !== 'closed'
            ) {
                return back()->with(
                    'error',
                    'Ticket yang sudah resolved tidak dapat diubah lagi.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CLOSED hanya boleh ADMIN
        |--------------------------------------------------------------------------
        */
        if (
            $newStatus === 'closed' &&
            !auth()->user()->isAdmin()
        ) {
            return back()->with(
                'error',
                'Hanya admin yang dapat menutup ticket.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Set resolved_at
        |--------------------------------------------------------------------------
        */
        if ($newStatus === 'resolved') {
            $ticket->resolved_at = now();
        }

        $ticket->status = $newStatus;
        $ticket->save();

        ActivityLogger::log(
            $ticket->id,
            'status_changed',
            "Changed status from {$oldStatus} to {$ticket->status}"
        );

        return back()->with(
            'success',
            'Status ticket berhasil diperbarui.'
        );
    }

    public function assign(
        Request $request,
        Ticket $ticket
    )
    {
        if(
            !auth()->user()->isAdmin()
            &&
            !auth()->user()->isAgent()
        ){
            abort(403);
        }

        $validated=$request->validate([
            'assignee_id'=>[
                'nullable',
                'exists:users,id'
            ],
        ]);
        $assignee=User::find(
            $validated['assignee_id']
        );

        if(
            $assignee
            &&
            !in_array(
                $assignee->role,
                [
                    'admin',
                    'agent'
                ]
            )
        ){
            return back()
                ->with(
                    'error',
                    'Assignee tidak valid.'
                );
        }
        $ticket->update([
            'assignee_id'=>
                $validated['assignee_id'],
        ]);

        ActivityLogger::log(
            $ticket->id,
            'assigned',
            'Assigned ticket to '.
            (
                $assignee
                ?
                $assignee->name
                :
                'unassigned'
            )
        );
        return back()
            ->with(
                'success',
                'Ticket berhasil diassign.'
            );
    }
}