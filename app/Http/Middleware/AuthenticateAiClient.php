<?php

namespace App\Http\Middleware;

use App\Exceptions\AiGatewayException;
use App\Models\AiApplication;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthenticateAiClient
{
    public function handle(Request $request, Closure $next)
    {
        $requestId = $this->newRequestId();
        $request->attributes->set('ai_request_id', $requestId);

        $token = $request->bearerToken();

        if (! is_string($token) || $token === '' || strlen($token) > 128 || ! $this->looksLikeClientKey($token)) {
            return AiGatewayException::invalidApiKey($requestId)->toResponse();
        }

        $hash = AiApplication::hashKey($token);
        $application = AiApplication::query()
            ->where('api_key_hash', $hash)
            ->first();

        if (! $application || ! hash_equals((string) $application->api_key_hash, $hash)) {
            return AiGatewayException::invalidApiKey($requestId)->toResponse();
        }

        if (! $application->isActive()) {
            return AiGatewayException::inactive($requestId)->toResponse();
        }

        $request->attributes->set('ai_application', $application);

        $response = $next($request);
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }

    private function looksLikeClientKey(string $token): bool
    {
        return str_starts_with($token, 'sk-');
    }

    private function newRequestId(): string
    {
        $prefix = (string) config('ai.request_id_prefix', 'req_ai_');

        if (method_exists(Str::class, 'ulid')) {
            return $prefix.strtolower((string) Str::ulid());
        }

        return $prefix.strtolower(bin2hex(random_bytes(13)));
    }
}
