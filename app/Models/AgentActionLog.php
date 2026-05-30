<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentActionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tool',
        'model_key',
        'arguments',
        'result',
        'status',
        'llm_model',
        'total_duration_ns',
        'eval_count',
        'eval_duration_ns',
    ];

    protected $casts = [
        'arguments' => 'array',
        'result' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
