<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AiRequest extends Model
{
    use HasFactory;
    use HasUuid;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';

    protected $fillable = [
        'uuid',
        'request_id',
        'ai_application_id',
        'model',
        'stream',
        'status',
        'http_status',
        'latency_ms',
        'error_code',
        'error_message',
        'end_user_id',
        'end_user_name',
        'end_user_email',
        'feature',
        'session_id',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'stream' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(AiApplication::class, 'ai_application_id');
    }

    public function usage(): HasOne
    {
        return $this->hasOne(AiUsage::class);
    }

    public function scopeForEndUser($query, string $term)
    {
        $like = '%'.$term.'%';

        return $query->where(function ($q) use ($term, $like) {
            $q->where('end_user_id', $term)
                ->orWhere('end_user_name', 'like', $like)
                ->orWhere('end_user_email', 'like', $like);
        });
    }
}
