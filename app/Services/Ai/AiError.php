<?php

namespace App\Services\Ai;

class AiError
{
    public const INVALID_API_KEY = 'INVALID_API_KEY';
    public const APPLICATION_INACTIVE = 'APPLICATION_INACTIVE';
    public const MODEL_NOT_FOUND = 'MODEL_NOT_FOUND';
    public const MODEL_NOT_ALLOWED = 'MODEL_NOT_ALLOWED';
    public const RATE_LIMIT_EXCEEDED = 'RATE_LIMIT_EXCEEDED';
    public const QUOTA_EXCEEDED = 'QUOTA_EXCEEDED';
    public const UPSTREAM_TIMEOUT = 'UPSTREAM_TIMEOUT';
    public const UPSTREAM_UNAVAILABLE = 'UPSTREAM_UNAVAILABLE';
    public const UPSTREAM_ERROR = 'UPSTREAM_ERROR';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
}
