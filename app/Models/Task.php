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
     * Limit to tasks whose type matches the current user's role type(s).
     * Admin / Super Admin are exempt — they see every task.
     * For other roles: no matching role type -> no tasks (strict task.type === role.type).
     */
    public function scopeForMyType($query)
    {
        if (is_task_manager()) {
            return $query; // managers see all tasks
        }
        $types = my_task_types();
        if (empty($types)) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereIn('type', $types);
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
}
