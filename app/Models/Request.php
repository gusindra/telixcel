<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Request extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_id',
        'reply',
        'from',
        'user_id',
        'team_id',
        'type',
        'template_id',
        'context_id',
        'identity',
        'media',
        'client_id',
        'sent_at',
        'is_read',
        'is_closed',
        'status'
    ];

    protected $appends = ['date'];

    /**
     * Get the action that belongs to client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'uuid');
    }

    /**
     * Get the action that belongs to agent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('App\Models\User', 'from', 'id');
    }

    /**
     * Get the action that belongs to owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /**
     * getLastUpdateAttribute
     *
     * @return void
     */
    public function getDateAttribute() {
		$date = Carbon::parse($this->created_at); // now date is a carbon instance
		return Carbon::make($date)->diffForHumans();
    }

    /**
     * Get all of the teams for the template.
     */
    public function teams()
    {
        return $this->morphToMany('App\Models\Team', 'teamable');
    }

    /**
     * Get the teams for the api.
     */
    public function team()
    {
        return $this->morphOne('App\Models\Teamable', 'teamable');
    }

    /**
     * tickets
     *
     * @return void
     */
    public function tickets(){
        return $this->hasMany('App\Models\Ticket', 'request_id','id')->where('status', '!=', 'close');
    }

    /**
     * active tickets
     *
     * @return void
     */
    public function activeTickets(){
        return $this->hasMany('App\Models\Ticket', 'request_id','id')->whereIn('status', ['open','waiting','handled']);
    }

    /**
     * Scope a query to only include inbounce requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInbounce($query)
    {
        return $query->whereNotNull('source_id');
    }

    /**
     * Scope a query to only include outbounce requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutbounce($query)
    {
        return $query->whereNull('source_id');
    }

    /**
     * Scope a query to only include outbounce requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCallapi($query)
    {
        return $query->where('from','api');
    }
}
