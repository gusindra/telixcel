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
        'expired_at'
    ];

    protected $guarded = [];
}
