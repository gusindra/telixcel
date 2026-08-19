<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = [
        'base_url',
        'api_key',
    ];

    /**
     * The OpenAI-compatible upstream base URL stored via the Settings page.
     * When empty, the application falls back to AI_BASE_URL / AI_API_KEY from .env.
     */
    public static function stored(): ?self
    {
        return static::query()->first();
    }
}
