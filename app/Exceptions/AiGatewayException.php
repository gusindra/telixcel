<?php

namespace App\Exceptions;

use App\Services\Ai\AiError;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class AiGatewayException extends RuntimeException
{
    /** @var string */
    public $errorCode;

    /** @var int */
    public $status;

    /** @var string|null */
    public $requestId;

    public function __construct(string $errorCode, string $message, int $status = 400, ?string $requestId = null)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->status = $status;
        $this->requestId = $requestId;
    }

    public static function invalidApiKey(?string $requestId = null): self
    {
        return new self(AiError::INVALID_API_KEY, 'Invalid API key.', 401, $requestId);
    }

    public static function inactive(?string $requestId = null): self
    {
        return new self(AiError::APPLICATION_INACTIVE, 'This application is inactive.', 403, $requestId);
    }

    public static function modelNotFound(string $model, ?string $requestId = null): self
    {
        return new self(AiError::MODEL_NOT_FOUND, 'The requested model is not available: '.$model, 404, $requestId);
    }

    public static function modelNotAllowed(?string $requestId = null): self
    {
        return new self(
            AiError::MODEL_NOT_ALLOWED,
            'This application is not allowed to use the requested model.',
            403,
            $requestId
        );
    }

    public static function rateLimited(?string $requestId = null): self
    {
        return new self(AiError::RATE_LIMIT_EXCEEDED, 'Too many requests.', 429, $requestId);
    }

    public static function quotaExceeded(string $kind, ?string $requestId = null): self
    {
        return new self(AiError::QUOTA_EXCEEDED, 'Monthly '.$kind.' quota exceeded.', 403, $requestId);
    }

    public static function validation(string $message, ?string $requestId = null): self
    {
        return new self(AiError::VALIDATION_ERROR, $message, 422, $requestId);
    }

    public static function upstreamTimeout(?string $requestId = null): self
    {
        return new self(AiError::UPSTREAM_TIMEOUT, 'The AI provider request timed out.', 504, $requestId);
    }

    public static function upstreamUnavailable(?string $requestId = null): self
    {
        return new self(AiError::UPSTREAM_UNAVAILABLE, 'The AI provider is unavailable.', 502, $requestId);
    }

    public static function upstreamError(string $message = 'The AI provider returned an error.', ?string $requestId = null): self
    {
        return new self(AiError::UPSTREAM_ERROR, $message, 502, $requestId);
    }

    public static function internal(?string $requestId = null): self
    {
        return new self(AiError::INTERNAL_ERROR, 'An internal error occurred.', 500, $requestId);
    }

    public function toResponse(): JsonResponse
    {
        $response = response()->json([
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->getMessage(),
                'request_id' => $this->requestId,
            ],
        ], $this->status);

        if ($this->requestId) {
            $response->headers->set('X-Request-ID', $this->requestId);
        }

        if ($this->errorCode === AiError::RATE_LIMIT_EXCEEDED) {
            $response->headers->set('Retry-After', '60');
        }

        return $response;
    }
}
