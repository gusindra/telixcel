<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'parent_id',
        'ticket_id',
        'title',
        'type',
        'priority',
        'owner_id',
        'assigned_to',
        'source',
        'target_date',
        'status',
        'status_note',
        'team_id',
    ];

    protected $casts = [
        'target_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** The ticket this task was created from (nullable). */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Tasks the current user may see in lists (Assistant project To-do, dashboard, etc.):
     *  - Super Admin (active) → everything
     *  - Always: tasks they own OR are assigned to (any type)
     *  - Plus: tasks whose type matches the active role (admin/finance/operasional)
     *
     * Previously assigned tasks of a different type were hidden — that made
     * "someone assigned a task to me but I can't see it" a real bug.
     */
    public function scopeForMyType($query)
    {
        if (is_task_manager()) {
            return $query; // Super Admin sees all tasks
        }

        $uid = auth()->id();
        $types = my_task_types();

        return $query->where(function ($w) use ($uid, $types) {
            // Always show work that belongs to this user, regardless of type.
            $w->where('owner_id', $uid)
              ->orWhere('assigned_to', $uid);

            // Plus role-type matches (other people's tasks in my lane).
            if (! empty($types)) {
                $w->orWhereIn('type', $types);
            }
        });
    }

    /** Direct child tasks (one level). */
    public function children()
    {
        return $this->hasMany(Task::class, 'parent_id')->orderBy('created_at');
    }

    /** All descendants recursively (for eager loading with nested::with). */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /** Parent task (null for root tasks where parent_id = 0). */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /** The user assigned to this task. */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
