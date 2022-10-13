<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'type',
        'name',
        'description',
        'trigger_condition',
        'trigger',
        'order',
        'template_id',
        'is_enabled',
        'user_id',
        'error_template_id',
        'is_wait_for_chat'
    ];

    protected $guarded = [];

    /**
     * Template has many Action
     *
     * @return void
     */
    public function actions(){
    	return $this->hasMany('App\Models\Action', 'template_id')->orderBy('order', 'asc');
    }

    /**
     * Template has one Endpoint
     *
     * @return void
     */
    public function endpoint(){
    	return $this->hasOne('App\Models\Endpoint', 'template_id');
    }

    /**
     * Template has many anwser from template
     *
     * @return void
     */
    public function anwsers(){
    	return $this->hasMany('App\Models\Template', 'template_id');
    }

    /**
     * Question Template is belong to Template
     * to know what is question for this template
     *
     * @return void
     */
    public function question(){
    	return $this->belongsTo('App\Models\Template', 'template_id', 'id');
    }
    public function children(){
    	return $this->belongsTo('App\Models\Template', 'template_id', 'id')->select('template_');
    }

    /**
     * Template has one Error Template
     *
     * @return void
     */
    public function error(){
    	return $this->hasOne('App\Models\Template', 'id','error_template_id');
    }

    /**
     * This error Template is belong to Template
     * to know what the template
     *
     * @return void
     */
    public function errorfor(){
    	return $this->belongsTo('App\Models\Template', 'error_template_id', 'id');
    }

    public function questionError(){
    	return $this->hasMany('App\Models\Template', 'error_template_id');
    }

    /**
     * Get all of the teams for the template.
     */
    public function teams()
    {
        return $this->morphToMany('App\Models\Team', 'teamable');
    }

    /**
     * This template created by
     * to know what the template
     *
     * @return void
     */
    public function createdBy(){
    	return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * scope Waiting
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeWaiting($query)
    {
        return $query->where('is_wait_for_chat', 1);
    }
}
