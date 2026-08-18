<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'ai_session_id',
    ];

    /** Messages (request/response turns) in this chat session, oldest first. */
    public function messages()
    {
        return $this->hasMany(AgentChatMessage::class)->orderBy('id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
