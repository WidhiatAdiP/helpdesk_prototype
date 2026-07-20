<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:2000'],
            'image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comment-images', 'public');
        }

        $ticket->comments()->create([
            'user_id'    => auth()->id(),
            'comment'    => $validated['comment'],
            'image_path' => $imagePath,
        ]);

        return back()->with('success', 'Comment added successfully.');
    }
}