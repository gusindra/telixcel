<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\HasProfilePhoto;

class Client extends Model
{
    use HasFactory;
    use HasProfilePhoto;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'sender', 'name', 'phone', 'identity', 'user_id', 'note', 'tag', 'email', 'address', 'title'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url', 'date', 'active'
    ];

    /**
     * Get the action that Client has manny Request .
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requests(){
    	return $this->hasMany(Request::class, 'from')->whereNotNull('source_id');
    }

    /**
     * Get the first Request of client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lastestRequest(){
    	return $this->hasOne('App\Models\Request', 'from')->whereNotNull('source_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the first Request of client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newestRequest(){
    	return $this->hasOne('App\Models\Request', 'client_id', 'uuid')->orderBy('id', 'desc');
    }

    /**
     * Get the action that Client has manny Request .
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notice(){
    	return $this->hasOne('App\Models\Request', 'client_id', 'uuid')->orderBy('id', 'desc');
    }

    /**
     * get Updated message Date
     *
     * @return void
     */
    public function getDateAttribute()
    {
        if($this->lastestRequest){
            return $this->lastestRequest->created_at;
        }
        return $this->created_at;
    }

    /**
     * get is active message
     *
     * @return void
     */

    public function getActiveAttribute()
    {
        if($this->notice){
            if($this->notice->source_id!==NULL){
                return $this->notice;
            }
        }
        return false;
    }

    /**
     * Get all of the teams for the customer.
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
     * Get the teams for the api.
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'email', 'email');
    }
}
