<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsage extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'ai_usage';

    protected $fillable = [
        'uuid',
        'ai_application_id',
        'ai_request_id',
        'model',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'cost',
        'currency',
        'end_user_id',
        'end_user_name',
        'feature',
    ];

    protected $casts = [
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'total_tokens' => 'integer',
        'cost' => 'float',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(AiApplication::class, 'ai_application_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(AiRequest::class, 'ai_request_id');
    }
}
