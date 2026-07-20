<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'assignee_id',
        'title',
        'description',
        'status',
        'priority',
        'category',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)
            ->latest();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}