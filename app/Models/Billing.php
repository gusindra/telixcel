<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'uuid',
        'code',
        'period',
        'description',
        'status',
        'amount',
        'user_id',
        'currency',
        'note'
    ];

    public static $searchable=[
        "code",
        "description"
    ];

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * Get next approval.
     */
    public function approval(){
    	return $this->hasOne('App\Models\FlowProcess', 'model_id')->where('model', 'INVOICE')->whereNull('status');
    }

    /**
     * Get all of team.
     */
    public function commission(){
    	return $this->hasOne('App\Models\Commision', 'model_id', 'order_id')->where('model', 'order');
    }
}
