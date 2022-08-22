<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommerceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'name',
        'spec',
        'source_id',
        'type',
        'unit',
        'description',
        'general_discount',
        'fs_price',
        'unit_price',
        'way_import',
        'status',
        'product_line',
        'user_id'
    ];

    public static $searchable=[
        "sku",
        "name"
    ];

    protected $guarded = [];

    /**
     * Get the action that belongs to product line.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productLine()
    {
        return $this->belongsTo('App\Models\ProductLine');
    }
}
