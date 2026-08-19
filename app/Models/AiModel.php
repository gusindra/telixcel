<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'uuid',
        'model_identifier',
        'display_name',
        'provider',
        'enabled',
        'verified_at',
        'description',
        'input_price_per_million',
        'output_price_per_million',
        'currency',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'verified_at' => 'datetime',
        'input_price_per_million' => 'float',
        'output_price_per_million' => 'float',
    ];

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(AiApplication::class, 'ai_application_model');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(AiRequest::class, 'model', 'model_identifier');
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeUsable($query)
    {
        return $query->where('enabled', true)->whereNotNull('verified_at');
    }

    public function isUsable(): bool
    {
        return (bool) $this->enabled && $this->verified_at !== null;
    }

    public function hasPricing(): bool
    {
        return $this->input_price_per_million !== null || $this->output_price_per_million !== null;
    }

    public function sourceKey(): string
    {
        $provider = strtolower(trim((string) $this->provider));
        if ($provider !== '' && $provider !== 'upstream') {
            return $provider;
        }

        $id = (string) $this->model_identifier;

        return str_contains($id, '/')
            ? strtolower((string) strtok($id, '/'))
            : 'upstream';
    }

    public function sourceLabel(): string
    {
        $key = $this->sourceKey();
        $labels = (array) config('ai.providers', []);

        if (isset($labels[$key]) && is_string($labels[$key])) {
            return $labels[$key];
        }

        return $key !== ''
            ? \Illuminate\Support\Str::title(str_replace(['-', '_'], ' ', $key))
            : 'Upstream';
    }

    public function familyKey(): string
    {
        return self::familyKeyOf((string) $this->model_identifier);
    }

    public static function familyKeyOf(string $identifier): string
    {
        $identifier = strtolower(trim($identifier));
        if ($identifier === '') {
            return '';
        }

        return str_contains($identifier, '/')
            ? substr($identifier, strrpos($identifier, '/') + 1)
            : $identifier;
    }

    public function publicName(): string
    {
        $name = trim((string) $this->display_name);
        $id = (string) $this->model_identifier;
        if ($name !== '' && ! str_contains($name, '/') && strcasecmp($name, $id) !== 0) {
            return $name;
        }

        return self::prettyName($id);
    }

    public static function prettyName(string $identifier): string
    {
        $slug = str_contains($identifier, '/')
            ? substr($identifier, strrpos($identifier, '/') + 1)
            : $identifier;
        $slug = trim((string) preg_replace('/[\-_]+/', ' ', $slug));
        if ($slug === '') {
            return $identifier;
        }

        $special = [
            'gpt' => 'GPT',
            'claude' => 'Claude',
            'gemini' => 'Gemini',
            'haiku' => 'Haiku',
            'sonnet' => 'Sonnet',
            'opus' => 'Opus',
            'flash' => 'Flash',
            'pro' => 'Pro',
            'mini' => 'Mini',
        ];

        $words = [];
        foreach (explode(' ', strtolower($slug)) as $word) {
            if ($word === '') {
                continue;
            }
            if (isset($special[$word])) {
                $words[] = $special[$word];
            } elseif (preg_match('/^\d/', $word)) {
                $words[] = $word;
            } else {
                $words[] = ucfirst($word);
            }
        }

        return implode(' ', $words) ?: $identifier;
    }
}
