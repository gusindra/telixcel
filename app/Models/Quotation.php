<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'title',
        'description',
        'quote_no',
        'commerce_id',
        'source_id',
        'client_id',
        'model',
        'model_id',
        'terms',
        'discount',
        'price',
        'status',
        'valid_day',
        'date',
        'user_id',
        'created_by',
        'created_role',
        'addressed_name',
        'addressed_role',
        'addressed_company',
    ];

    public static $searchable=[
        "title",
        "quote_no"
    ];

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
        'expired_date' => 'datetime',
    ];

    /**
     * Get the action that belongs to template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function endpoint()
    // {
    //     return $this->belongsTo('App\Models\Endpoint');
    // }

    public function items(){
        return $this->hasMany('App\Models\OrderProduct', 'model_id')->where('model', 'Quotation');
    }

    /**
     * Get all of team.
     */
    public function approval(){
    	return $this->hasOne('App\Models\FlowProcess', 'model_id')->where('model', 'quotation')->whereNull('status');
    }
    public function userApproval(){
    	return $this->hasMany('App\Models\FlowProcess', 'model_id')->where('model', 'quotation')->whereNotNull('user_id')->groupBy('user_id');
    }

    // source model
    public function company()
    {
    	return $this->belongsTo('App\Models\Company', 'model_id');
    }
    public function project()
    {
        return $this->belongsTo('App\Models\Project', 'model_id');
    }
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'model_id');
    }
    public function order()
    {
        return $this->hasOne('App\Models\Order', 'source_id')->where('source', 'QUOTATION');
    }

    /**
     * attachment sign document
     *
     * @return void
     */
    public function attachments(){
        return $this->hasMany('App\Models\Attachment', 'model_id')->where('model', 'quotation');
    }

    protected $appends = ['expired_date'];

    public function getExpiredDateAttribute(){
        $date = Carbon::createFromFormat('Y-m-d',  $this->date->format('Y-m-d'));
        $daysToAdd = $this->valid_day;
        return $date = $date->addDays($daysToAdd);
    }
}
