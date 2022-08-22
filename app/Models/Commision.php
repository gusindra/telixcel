<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commision extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model',
        'model_id',
        'client_id',
        'type',
        'ratio',
        'total',
        'status',
    ];

    protected $guarded = [];

     /**
     * Get the flow that belongs to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\CommerceItem', 'model_id')->where('model', 'product');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project', 'model_id')->where('model', 'project');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'model_id')->where('model', 'order');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Order', 'model_id')->where('model', 'invoice');
    }

    public function agent()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    /**
     * Get all of team.
     */
    public function approval(){
    	return $this->hasOne('App\Models\FlowProcess', 'model_id')->where('model', 'COMMISSION')->whereNull('status');
    }
}
