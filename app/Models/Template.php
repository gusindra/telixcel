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
    ];

    protected $guarded = [];

    public function actions(){
    	return $this->hasMany('App\Models\Action', 'template_id');
    }

    public function endpoint(){
    	return $this->hasOne('App\Models\Endpoint', 'template_id');
    }

    public function anwsers(){
    	return $this->hasMany('App\Models\Template', 'template_id');
    }

    public function question(){
    	return $this->belongsTo('App\Models\Template', 'template_id', 'id');
    }

    public function errors(){
    	return $this->hasOne('App\Models\Template', 'error_template_id');
    }

    public function errorfor(){
    	return $this->belongsTo('App\Models\Template', 'error_template_id', 'id');
    }
}
