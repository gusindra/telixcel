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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'user_name', 'client_name'
    ];

    /**
     * get is user name
     *
     * @return void
     */

    public function getUserNameAttribute()
    {
        if($this->user_id){
            if($this->user->name!==NULL){
                return $this->user->name;
            }
        }
        return '';
    }

    /**
     * get is client name
     *
     * @return void
     */

    public function getClientNameAttribute()
    {
        if($this->client_id){
            if($this->client->name!==NULL){
                return $this->client->name;
            }
        }
        return '';
    }

    /**
     * Get the action that belongs to client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'uuid');
    }

    /**
     * Get the action that belongs to client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
