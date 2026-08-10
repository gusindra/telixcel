<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Per-user API token for Hermes → Laravel /api/agent.
 * Minted on first AI Console chat: read + limited update by default.
 */
class AgentUserToken extends Model
{
    protected $fillable = [
        'user_id',
        'token_hash',
        'token_prefix',
        'token_encrypted',
        'abilities',
        'last_used_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
    ];

    /** Default scopes when a user first opens AI chat. */
    public const DEFAULT_ABILITIES = [
        'read',           // query_records, schema, health, generate_report (read data)
        'update_limited', // update_record → still returns pending for UI approval
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plainToken(): string
    {
        return Crypt::decryptString($this->token_encrypted);
    }

    public function can(string $ability): bool
    {
        $abilities = $this->abilities ?? [];

        return in_array($ability, $abilities, true);
    }

    public function allowsTool(string $tool): bool
    {
        return match ($tool) {
            'query_records', 'generate_report' => $this->can('read'),
            'update_record' => $this->can('update_limited') || $this->can('update'),
            'download_report' => $this->can('read'),
            default => false,
        };
    }

    /**
     * Get or create token for user. Returns [model, plainToken].
     *
     * @return array{0: self, 1: string}
     */
    public static function ensureForUser(User $user, ?array $abilities = null): array
    {
        $row = static::where('user_id', $user->id)->first();
        if ($row) {
            return [$row, $row->plainToken()];
        }

        $plain = 'agt_' . Str::random(40);
        $row = static::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plain),
            'token_prefix' => substr($plain, 0, 12),
            'token_encrypted' => Crypt::encryptString($plain),
            'abilities' => $abilities ?? self::DEFAULT_ABILITIES,
        ]);

        return [$row, $plain];
    }

    public static function findByPlainToken(string $plain): ?self
    {
        $plain = trim($plain);
        if ($plain === '') {
            return null;
        }

        return static::where('token_hash', hash('sha256', $plain))->first();
    }

    public function touchLastUsed(): void
    {
        $this->forceFill(['last_used_at' => now()])->save();
    }
}
