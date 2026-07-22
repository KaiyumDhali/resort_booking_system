<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Task extends Model
{
    protected $fillable = [
        'title', 'description',
        'issue_date', 'end_date',
        'budget', 'actual_cost',
        'priority', 'status',
        'approved_by', 'completion_date',
        'remarks',
        'is_carried_forward', 'carried_from_id',
        'created_by',
    ];

    protected $casts = [
        'issue_date'         => 'date',
        'end_date'           => 'date',
        'completion_date'    => 'date',
        'is_carried_forward' => 'boolean',
    ];

    // যে task থেকে carry হয়েছে
    public function carriedFrom()
    {
        return $this->belongsTo(Task::class, 'carried_from_id');
    }

    // ✅ এই task-এ কোন কোন employee assign করা হয়েছে
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'task_employee', 'task_id', 'employee_id')
            ->withTimestamps();
    }



public function users()
{
    return $this->belongsToMany(\App\Models\User::class, 'task_user');
}

    // Overdue check
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'completed'
            && $this->status !== 'cancelled'
            && $this->end_date < now()->toDateString();
    }

    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function updater()
{
    return $this->belongsTo(User::class, 'updated_by');
}

public function approver()
{
    return $this->belongsTo(User::class, 'approved_by');
}

public function completer()
{
    return $this->belongsTo(User::class, 'completed_by');
}
}