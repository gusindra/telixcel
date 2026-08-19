<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiApplication extends Model
{
    use HasFactory;
    use HasUuid;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_IDR = 'IDR';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'api_key_hash',
        'api_key_prefix',
        'api_key_encrypted',
        'status',
        'rate_limit_per_minute',
        'monthly_token_limit',
        'monthly_cost_limit',
        'cost_currency',
        'require_end_user',
    ];

    protected $hidden = [
        'api_key_hash',
        'api_key_encrypted',
    ];

    protected $casts = [
        'require_end_user' => 'boolean',
        'rate_limit_per_minute' => 'integer',
        'monthly_token_limit' => 'integer',
        'monthly_cost_limit' => 'float',
    ];

    public function models(): BelongsToMany
    {
        return $this->belongsToMany(AiModel::class, 'ai_application_model');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(AiRequest::class);
    }

    public function usage(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function costCurrency(): string
    {
        $currency = strtoupper((string) $this->cost_currency);

        return $currency === self::CURRENCY_IDR ? self::CURRENCY_IDR : self::CURRENCY_USD;
    }

    public function usdToLimitCurrency(float $usd): float
    {
        if ($this->costCurrency() === self::CURRENCY_IDR) {
            return $usd * (float) config('ai.usd_idr', 16500);
        }

        return $usd;
    }

    public function formatCost(?float $usd): string
    {
        $amount = $this->usdToLimitCurrency((float) $usd);
        if ($this->costCurrency() === self::CURRENCY_IDR) {
            return 'Rp '.number_format($amount, 0, ',', '.');
        }

        return '$'.number_format($amount, 2);
    }

    /**
     * Issue a new client key. The hash is used for auth; an encrypted copy
     * can be revealed in admin after password confirmation.
     */
    public function issueKey(): string
    {
        $raw = 'sk-'.bin2hex(random_bytes(24));
        $this->api_key_hash = hash('sha256', $raw);
        $this->api_key_prefix = substr($raw, 0, 16);
        $this->api_key_encrypted = encrypt($raw);

        return $raw;
    }

    public function revealedKey(): ?string
    {
        if (! $this->api_key_encrypted) {
            return null;
        }

        try {
            $raw = decrypt($this->api_key_encrypted);
        } catch (\Throwable $e) {
            return null;
        }

        if (! is_string($raw) || $raw === '' || ! hash_equals((string) $this->api_key_hash, self::hashKey($raw))) {
            return null;
        }

        return $raw;
    }

    public static function hashKey(string $raw): string
    {
        return hash('sha256', $raw);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
