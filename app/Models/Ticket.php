<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reasons',
        'solution',
        'status',
        'priority',
        'created_by',
        'updated_by',
        'request_id',
        'handled_by',
        'role_id',
        'forward_to',
        'resolved_at',
        'closed_at',
    ];

    protected $guarded = [];

    protected $dates = [ 'deleted_at' ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    /** The user assigned to handle this ticket. */
    public function assignee()
    {
        return $this->belongsTo('App\Models\User', 'handled_by');
    }

    /** The role this ticket is assigned to. */
    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    /**
     * Get the action that belongs to template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function request()
    {
        return $this->belongsTo('App\Models\Request', 'request_id');
    }

    /** To-do tasks created from this ticket. */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'ticket_id');
    }

    /**
     * Get the action that belongs to template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    /**
     * Get the action that belongs to template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo('App\Models\User', 'updated_by');
    }

    /**
     * Get the action that belongs to forward user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function forwardUser()
    {
        return $this->belongsTo('App\Models\User', 'forward_to');
    }

     /**
     * scope Active
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'handle', 'waiting']);
    }
}
