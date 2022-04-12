<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'model_id',
        'model',
        'notification',
        'user_id',
        'status',
    ];

    protected $appends = ['date'];

    protected $guarded = [];

    protected $dates = [ 'deleted_at' ];

    /**
     * Get the action that belongs to template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Get the action that belongs to Models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        if($this->model=='Ticket')
            return $this->belongsTo('App\Models\Ticket', 'model_id');
        return false;
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

    /**
     * Scope a query to only include outbounce requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNew($query)
    {
        return $query->where('status','new');
    }
}
