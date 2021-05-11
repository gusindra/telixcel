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
        'type',
        'template_id',
        'context_id',
        'identity',
        'media',
        'client_id',
        'sent_at',
        'is_read'
    ];

    protected $appends = ['date'];

    /**
     * Get the action that belongs to client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id', 'uuid');
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
		$date = Carbon::parse($this->updated_at); // now date is a carbon instance
		return Carbon::make($date)->diffForHumans();
    }
}
