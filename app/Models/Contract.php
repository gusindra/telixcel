<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'status',
        'signer_email',
        'original_attachment',
        'result_attachment',
        'user_id',
        'actived_at',
        'expired_at',
        'model',
        'model_id'
    ];

    public static $searchable=[
        "title"
    ];

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'actived_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * Relation ship project / client
     *
     * @return void
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project', 'model_id');
    }
    /**
     * client
     *
     * @return void
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'model_id');
    }
    /**
     * Get last approval.
     */
    public function approval(){
    	return $this->hasOne('App\Models\FlowProcess', 'model_id')->where('model', 'CONTRACT')->whereNull('status');
    }
    public function userApproval(){
    	return $this->hasMany('App\Models\FlowProcess', 'model_id')->where('model', 'CONTRACT')->whereNotNull('user_id')->groupBy('user_id');
    }
    /**
     * attachment order
     *
     * @return void
     */
    public function attachments(){
        return $this->hasMany('App\Models\Attachment', 'model_id')->where('model', 'contract');
    }
}
