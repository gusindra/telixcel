<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_chat_id',
        'role',
        'content',
    ];

    public function chat()
    {
        return $this->belongsTo(AgentChat::class, 'agent_chat_id');
    }
}
