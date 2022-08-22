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
        'referrer_name',
        'team_id',
        'product_line'
    ];

    public static $searchable=[
        "name"
    ];

    protected $guarded = [];

    /**
     * Get all of customer.
     */
    public function customer(){
    	return $this->belongsTo('App\Models\Client');
    }

    /**
     * Get all of team.
     */
    public function team(){
    	return $this->belongsTo('App\Models\Team');
    }

    /**
     * Get the action that belongs to product line.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productLine()
    {
        return $this->belongsTo('App\Models\ProductLine', 'product_line');
    }

    /**
     * Get all of team.
     */
    public function approval(){
    	return $this->hasOne('App\Models\FlowProcess', 'model_id')->where('model', 'PROJECT')->whereNull('status');
    }

    /**
     * Get all contract of project.
     */
    public function contracts(){
        return $this->hasMany('App\Models\Contract', 'model_id')->where('model', 'PROJECT')->orderBy('updated_at', 'DESC');
    }

    /**
     * Get all contract of project.
     */
    public function orders(){
        return $this->hasMany('App\Models\Order', 'source_id')->where('source', 'PROJECT')->orderBy('updated_at', 'DESC');
    }

    /**
     * Get all contract of project.
     */
    public function quotations(){
        return $this->hasMany('App\Models\Quotation', 'model_id')->where('model', 'PROJECT')->orderBy('updated_at', 'DESC');
    }

    /**
     * Get company owner
     *
     * @return void
     */
    public function company(){
    	return $this->belongsTo('App\Models\Company', 'entity_party');
    }
}
