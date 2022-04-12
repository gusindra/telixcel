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
        'created_by',
        'updated_by',
        'request_id',
        'handled_by',
        'forward_to'
    ];

    protected $guarded = [];

    protected $dates = [ 'deleted_at' ];

    /**
     * Get the action that belongs to template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function request()
    {
        return $this->belongsTo('App\Models\Request', 'request_id');
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
