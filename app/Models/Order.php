<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
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
        'entity_party',
        'customer_type',
        'referrer_id',
        'commision_ratio',
        'total',
        'customer_id',
        'user_id',
    ];

    protected $guarded = [];

    /**
     * Get all of the permission that are assigned this role.
     */
    public function customer(){
    	return $this->belongsTo('App\Models\Client', 'customer_id');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'user_id');
    }
}
