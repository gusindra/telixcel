<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'uuid',
        'code',
        'period',
        'description',
        'status',
        'amount',
        'user_id',
        'currency',
        'note'
    ];

    protected $guarded = [];
}
