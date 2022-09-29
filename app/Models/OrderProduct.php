<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'model',
        'model_id',
        'product_id',
        'qty',
        'unit',
        'price',
        'total_percentage',
        'note',
        'user_id',
    ];

    protected $guarded = [];

    /**
     * Get all of the permission that are assigned this role.
     */
    public function product(){
    	return $this->belongsTo('App\Models\CommerceItem');
    }

    public function order(){
    	return $this->belongsTo('App\Models\Order');
    }

    public function quotation(){
    	return $this->belongsTo('App\Models\Quotation');
    }
}
