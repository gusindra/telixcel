<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'status',
        'entity_party',
        'customer_name',
        'customer_address',
        'contact_id',
        'team_id',
    ];

    protected $guarded = [];

    /**
     * Get all of the permission that are assigned this role.
     */
    public function customer(){
    	return $this->belongsTo('App\Models\Client');
    }

    public function team(){
    	return $this->belongsTo('App\Models\Team');
    }
}
