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
        'name',
        'commerce_id',
        'source_id',
        'model',
        'model_id',
        'terms',
        'discount',
        'price',
        'status',
    ];

    protected $guarded = [];

    /**
     * Get the action that belongs to template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function endpoint()
    // {
    //     return $this->belongsTo('App\Models\Endpoint');
    // }
}
