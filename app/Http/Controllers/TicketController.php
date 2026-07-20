<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Comment;
use App\Models\User;
use App\Models\Attachment;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $resolution = null;

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