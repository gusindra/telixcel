<?php

namespace App\Models;

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

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
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
}
