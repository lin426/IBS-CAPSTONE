<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TaskHandler;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assigned_staff_id',   // keep using this for staff
        'user_id',
        'project_id',
        // NEW for client portal:
        'client_user_id',      // which client user this task belongs to
        'client_rating',       // client's 1–5 rating of the assigned staff
    ];

    // (optional but nice) have Laravel cast these
    protected $casts = [
        'due_date'      => 'datetime',
        'client_rating' => 'integer',
    ];

    public function handler()
    {
        return $this->hasOne(TaskHandler::class);
    }

    // ✅ unchanged — keep using assigned_staff_id
    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // NEW: the client user who owns this task (used to filter tasks in client portal)
    public function clientUser()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    // (optional) if user_id is the creator/owner on admin side
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
