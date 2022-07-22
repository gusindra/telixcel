<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceSyn extends Model
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
        'source_id',
        'source',
        'status',
    ];

    protected $guarded = [];

     /**
     * Get the flow that belongs to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model()
    {
        if($this->model=='CommerceItem'){
            return $this->belongsTo('App\Models\CommerceItem', 'model_id');
        }elseif($this->model=='Warehouse'){
            return $this->belongsTo('App\Models\Warehouse', 'model_id');
        }elseif($this->model=='ProductLine'){
            return $this->belongsTo('App\Models\ProductLine', 'model_id');
        }
        return false;
    }
}
