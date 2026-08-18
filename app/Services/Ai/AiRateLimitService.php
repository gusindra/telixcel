<?php

namespace App\Services\Ai;

use App\Exceptions\AiGatewayException;
use App\Models\AiApplication;
use Illuminate\Support\Facades\RateLimiter;

class AiRateLimitService
{
    public function assertWithinLimit(AiApplication $application, ?string $requestId = null): void
    {
        $limit = $application->rate_limit_per_minute;
        if ($limit === null) {
            return;
        }

        $key = 'ai:rpm:'.$application->id;

        if (RateLimiter::tooManyAttempts($key, (int) $limit)) {
            throw AiGatewayException::rateLimited($requestId);
        }

        RateLimiter::hit($key, 60);
    }
}
