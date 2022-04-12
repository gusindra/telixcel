<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlastMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'msg_id',
        'user_id',
        'client_id',
        'status',
        'message_content',
        'balance',
        'msisdn',
    ];

    protected $guarded = [];

}
