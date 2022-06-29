<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLine extends Model
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
        'user_id'
    ];

    protected $guarded = [];

    /**
     * Get all item commerce of lines.
     */
    public function items(){
        return $this->hasMany('App\Models\CommerceItem', 'product_line');
    }
}
