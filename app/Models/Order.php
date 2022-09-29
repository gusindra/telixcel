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
        'no',
        'name',
        'type',
        'entity_party',
        'customer_type',
        'referrer_id',
        'commision_ratio',
        'vat',
        'total',
        'customer_id',
        'user_id',
        'source',
        'source_id',
        'status',
        'date'
    ];

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    public static $searchable=[
        "name",
        "no"
    ];

    /**
     * Get all of the permission that are assigned this role.
     */
    public function customer(){
    	return $this->belongsTo('App\Models\Client', 'customer_id', 'uuid');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function company(){
    	return $this->belongsTo('App\Models\Company', 'entity_party');
    }

    public function project(){
    	return $this->belongsTo('App\Models\Project', 'source_id');
    }

    public function invoice(){
        return $this->hasMany('App\Models\Billing', 'order_id');
    }

    public function lastInvoice(){
        return $this->hasOne('App\Models\Billing', 'order_id')->orderBy('period', 'desc');
    }

    /**
     * detail order
     *
     * @return void
     */
    public function items(){
        return $this->hasMany('App\Models\OrderProduct', 'model_id')->where('model', 'Order');
    }

    /**
     * notification order
     *
     * @return void
     */
    public function notifications($status=null){
        if(!is_null($status)){
            return $this->hasMany('App\Models\Notification', 'model_id')->where('model', 'Order')->where('status', $status);
        }
        return $this->hasMany('App\Models\Notification', 'model_id')->where('model', 'Order');
    }

    /**
     * attachment order
     *
     * @return void
     */
    public function attachments(){
        return $this->hasMany('App\Models\Attachment', 'model_id')->where('model', 'order');
    }

    /**
     * Get invoice data
     */
    public function bill(){
    	return $this->hasOne('App\Models\Billing', 'order_id');
    }

    /**
     * Get next approval.
     */
    public function approval(){
    	return $this->hasOne('App\Models\FlowProcess', 'model_id')->where('model', 'ORDER')->whereNull('status');
    }

    /**
     * Get all of team.
     */
    public function commission(){
    	return $this->hasOne('App\Models\Commision', 'model_id')->where('model', 'order');
    }

}
