<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function publicKey(): string
    {
        return (string) ($this->uuid ?: $this->getKey());
    }

    public static function findPublic($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (Str::isUuid((string) $value)) {
            return static::where('uuid', $value)->first();
        }

        if (ctype_digit((string) $value)) {
            return static::find($value);
        }

        return static::where('uuid', $value)->first();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        }

        $found = static::findPublic($value);
        if (! $found) {
            abort(404);
        }

        return $found;
    }
}
