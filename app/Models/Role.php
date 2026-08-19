<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'description',
        'role_for',
        'type',
        'status',
        'team_id',
    ];

    protected $guarded = [];

    /**
     * Get all of the permission that are assigned this role.
     */
    public function permission(){
    	return $this->belongsToMany('App\Models\Permission');
    }
    
    public function permission_by_model(){
    	return $this->belongsToMany('App\Models\Permission')->groupBy('model')->orderBy('id');
    }
}
