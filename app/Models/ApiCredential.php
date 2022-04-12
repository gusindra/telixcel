<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class ApiCredential extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_key',
        'server_key',
        'credential',
        'user_id',
        'client',
        'is_enabled',
    ];

    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['slug'];

    /**
     * Get the action that belongs to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * getSlugAttribute
     *
     * @return void
     */
    public function getSlugAttribute()
    {
        return Hashids::encode($this->id);
    }

    /**
     * Get all of the teams for the api.
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
     * Scope a query to only include outbounce requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_enabled', 1);
    }
}
