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
        'sender_id',
        'type',
        'status',
        'message_content',
        'balance',
        'msisdn',
        'title',
        'price',
        'code',
        'currency',
        'otp',
    ];

    protected $guarded = [];

    /**
     * Get the action that belongs to client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'uuid');
    }
}
