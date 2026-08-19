<?php

namespace App\Services\Ai;

use App\Exceptions\AiGatewayException;
use App\Jobs\Ai\ProcessAiUsage;
use App\Models\AiApplication;
use App\Models\AiModel;
use App\Models\AiRequest;
use App\Services\Ai\AiError;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiGatewayService
{
    private const PASSTHROUGH = [
        'model',
        'messages',
        'stream',
        'temperature',
        'top_p',
        'max_tokens',
        'max_completion_tokens',
        'stop',
        'presence_penalty',
        'frequency_penalty',
        'response_format',
        'user',
    ];

    public function __construct(
        private AiUpstreamService $router,
        private AiModelService $models,
        private AiPermissionService $permissions,
        private AiQuotaService $quota,
        private AiRateLimitService $rateLimits,
        private AiUsageService $usage,
        private AiMessageGuard $messages
    ) {
    }

    /**
     * @return JsonResponse|StreamedResponse
     */
    public function handle(Request $request)
    {
        /** @var AiApplication $application */
        $application = $request->attributes->get('ai_application');
        $requestId = (string) $request->attributes->get('ai_request_id');
        $startedAt = now();
        $actor = $this->resolveActor($request);

        try {
            $this->assertEndUser($application, $actor, $requestId);
            $this->rateLimits->assertWithinLimit($application, $requestId);

            $allowed = $this->permissions->allowedModelIds($application);
            $chain = $this->models->resolveChain((string) $request->input('model'), $allowed, $requestId);
            $model = $chain->first();
            $this->permissions->assertAllowed($application, $model, $requestId);
            $this->quota->assertWithinLimits($application, $requestId);
            $payload = $this->upstreamPayload($request, $model, $requestId);
        } catch (AiGatewayException $e) {
            $e->requestId = $e->requestId ?: $requestId;
            $this->logRejection($application, $request, $requestId, $actor, $startedAt, $e);
            throw $e;
        }

        $aiRequest = $this->startLog($application, $request, $requestId, $actor, $startedAt);

        if ($request->boolean('stream')) {
            return $this->stream($application, $chain, $aiRequest, $payload, $actor, $startedAt);
        }

        return $this->json($application, $chain, $aiRequest, $payload, $actor, $startedAt);
    }

    /**
     * @param  \Illuminate\Support\Collection<int,AiModel>  $chain
     */
    private function json(
        AiApplication $application,
        $chain,
        AiRequest $aiRequest,
        array $payload,
        array $actor,
        $startedAt
    ): JsonResponse {
        $last = null;
        $lastModel = $chain->last();

        foreach ($chain as $index => $model) {
            $attempt = $payload;
            $attempt['model'] = $model->model_identifier;

            try {
                $body = $this->router->chat($attempt);
                $extracted = $this->usage->extractFromUpstream(is_array($body) ? $body : []);
                $this->dispatchUsage($application, $model, $aiRequest, $actor, $startedAt, AiRequest::STATUS_SUCCESS, 200, $extracted);

                return response()->json($body)
                    ->header('X-Request-ID', $aiRequest->request_id);
            } catch (AiGatewayException $e) {
                $e->requestId = $aiRequest->request_id;
                $last = $e;
                $lastModel = $model;

                if (! $this->canFallback($e) || $index === $chain->count() - 1) {
                    break;
                }

                Log::warning('AI gateway trying next model', [
                    'request_id' => $aiRequest->request_id,
                    'failed_model' => $model->model_identifier,
                ]);
            }
        }

        $this->failAndDispatch($application, $lastModel, $aiRequest, $actor, $startedAt, $last, []);

        return $last->toResponse();
    }

    /**
     * @param  \Illuminate\Support\Collection<int,AiModel>  $chain
     */
    private function stream(
        AiApplication $application,
        $chain,
        AiRequest $aiRequest,
        array $payload,
        array $actor,
        $startedAt
    ): StreamedResponse {
        return response()->stream(function () use ($application, $chain, $aiRequest, $payload, $actor, $startedAt) {
            $this->prepareStreamOutput();
            ignore_user_abort(true);

            $buffer = '';
            $failed = null;
            $used = $chain->first();
            $started = false;

            foreach ($chain as $index => $model) {
                $used = $model;
                $attempt = $payload;
                $attempt['model'] = $model->model_identifier;
                $attempt['stream'] = true;
                $buffer = '';

                try {
                    foreach ($this->router->stream($attempt) as $chunk) {
                        $started = true;
                        $buffer .= $chunk;
                        echo $chunk;
                        $this->flushStream();
                        if (connection_aborted()) {
                            break;
                        }
                    }
                    $failed = null;
                    break;
                } catch (AiGatewayException $e) {
                    $failed = $e;
                    $e->requestId = $aiRequest->request_id;

                    if ($started || ! $this->canFallback($e) || $index === $chain->count() - 1) {
                        echo $this->sseError($e);
                        $this->flushStream();
                        break;
                    }

                    Log::warning('AI gateway trying next model', [
                        'request_id' => $aiRequest->request_id,
                        'failed_model' => $model->model_identifier,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('AI gateway stream failed', ['request_id' => $aiRequest->request_id]);
                    $failed = AiGatewayException::internal($aiRequest->request_id);
                    echo $this->sseError($failed);
                    $this->flushStream();
                    break;
                }
            }

            $extracted = $this->usage->extractFromSse($buffer);

            if ($failed) {
                $this->failAndDispatch($application, $used, $aiRequest, $actor, $startedAt, $failed, $extracted);

                return;
            }

            $this->dispatchUsage($application, $used, $aiRequest, $actor, $startedAt, AiRequest::STATUS_SUCCESS, 200, $extracted);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
            'X-Request-ID' => $aiRequest->request_id,
        ]);
    }

    private function canFallback(AiGatewayException $e): bool
    {
        if (! config('ai.fallback.enabled', true)) {
            return false;
        }

        return in_array($e->errorCode, [
            AiError::UPSTREAM_TIMEOUT,
            AiError::UPSTREAM_UNAVAILABLE,
            AiError::UPSTREAM_ERROR,
            AiError::MODEL_NOT_FOUND,
            AiError::RATE_LIMIT_EXCEEDED,
        ], true);
    }

    private function prepareStreamOutput(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', false);
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    private function flushStream(): void
    {
        if (ob_get_level() > 0) {
            @ob_flush();
        }
        @flush();
    }

    private function sseError(AiGatewayException $e): string
    {
        return 'data: '.json_encode([
            'error' => [
                'code' => $e->errorCode,
                'message' => $e->getMessage(),
                'request_id' => $e->requestId,
            ],
        ])."\n\n";
    }

    private function startLog(AiApplication $application, Request $request, string $requestId, array $actor, $startedAt): AiRequest
    {
        return AiRequest::create([
            'request_id' => $requestId,
            'ai_application_id' => $application->id,
            'model' => (string) $request->input('model'),
            'stream' => $request->boolean('stream'),
            'status' => AiRequest::STATUS_PENDING,
            'end_user_id' => $actor['end_user_id'],
            'end_user_name' => $actor['end_user_name'],
            'end_user_email' => $actor['end_user_email'],
            'feature' => $actor['feature'],
            'session_id' => $actor['session_id'],
            'started_at' => $startedAt,
        ]);
    }

    /**
     * @param  array<string,mixed>  $extracted
     */
    private function dispatchUsage(
        AiApplication $application,
        AiModel $model,
        AiRequest $aiRequest,
        array $actor,
        $startedAt,
        string $status,
        int $httpStatus,
        array $extracted,
        ?string $errorCode = null,
        ?string $errorMessage = null
    ): void {
        ProcessAiUsage::dispatch([
            'request_id' => $aiRequest->request_id,
            'ai_application_id' => $application->id,
            'model' => $model->model_identifier,
            'input_tokens' => $extracted['input_tokens'] ?? null,
            'output_tokens' => $extracted['output_tokens'] ?? null,
            'total_tokens' => $extracted['total_tokens'] ?? null,
            'cost' => $extracted['cost'] ?? null,
            'currency' => $extracted['currency'] ?? $model->currency,
            'end_user_id' => $actor['end_user_id'],
            'end_user_name' => $actor['end_user_name'],
            'feature' => $actor['feature'],
            'status' => $status,
            'http_status' => $httpStatus,
            'latency_ms' => $this->latencyMs($startedAt),
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'completed_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * @param  array<string,mixed>  $extracted
     */
    private function failAndDispatch(
        AiApplication $application,
        AiModel $model,
        AiRequest $aiRequest,
        array $actor,
        $startedAt,
        AiGatewayException $e,
        array $extracted
    ): void {
        $this->dispatchUsage(
            $application,
            $model,
            $aiRequest,
            $actor,
            $startedAt,
            AiRequest::STATUS_ERROR,
            $e->status,
            $extracted,
            $e->errorCode,
            $e->getMessage()
        );
    }

    private function upstreamPayload(Request $request, AiModel $model, string $requestId): array
    {
        $payload = [];

        foreach (self::PASSTHROUGH as $field) {
            if ($field === 'messages' || $field === 'model' || ! $request->exists($field)) {
                continue;
            }
            $payload[$field] = $request->input($field);
        }

        $payload['model'] = $model->model_identifier;
        $payload['messages'] = $this->messages->sanitize((array) $request->input('messages', []), $requestId);

        if (isset($payload['max_tokens'])) {
            $payload['max_tokens'] = min((int) $payload['max_tokens'], (int) config('ai.guard.max_tokens', 8192));
        }
        if (isset($payload['max_completion_tokens'])) {
            $payload['max_completion_tokens'] = min(
                (int) $payload['max_completion_tokens'],
                (int) config('ai.guard.max_tokens', 8192)
            );
        }

        if (isset($payload['stop'])) {
            $payload['stop'] = $this->sanitizeStop($payload['stop'], $requestId);
        }

        if (isset($payload['user']) && is_string($payload['user'])) {
            $payload['user'] = mb_substr($this->scrubScalar($payload['user']) ?? '', 0, 191);
            if ($payload['user'] === '') {
                unset($payload['user']);
            }
        }

        return $payload;
    }

    /**
     * @param  mixed  $stop
     * @return string|array<int,string>|null
     */
    private function sanitizeStop($stop, string $requestId)
    {
        if ($stop === null || $stop === '') {
            return null;
        }

        if (is_string($stop)) {
            $stop = $this->scrubScalar($stop);

            return $stop === null ? null : mb_substr($stop, 0, 64);
        }

        if (! is_array($stop)) {
            throw AiGatewayException::validation('Invalid stop value.', $requestId);
        }

        $out = [];
        foreach (array_slice($stop, 0, 4) as $item) {
            if (! is_string($item)) {
                throw AiGatewayException::validation('Invalid stop value.', $requestId);
            }
            $item = $this->scrubScalar($item);
            if ($item !== null) {
                $out[] = mb_substr($item, 0, 64);
            }
        }

        return $out === [] ? null : $out;
    }

    private function scrubScalar($value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = str_replace("\0", '', $value);
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function assertEndUser(AiApplication $application, array $actor, string $requestId): void
    {
        if ($application->require_end_user && empty($actor['end_user_id'])) {
            throw AiGatewayException::validation(
                'end_user_id is required for this application. Send metadata.end_user_id or the user field.',
                $requestId
            );
        }
    }

    /**
     * @return array{end_user_id:?string,end_user_name:?string,end_user_email:?string,feature:?string,session_id:?string}
     */
    private function resolveActor(Request $request): array
    {
        $meta = $request->input('metadata', []);
        if (! is_array($meta)) {
            $meta = [];
        }

        $id = $meta['end_user_id']
            ?? $request->header('X-End-User-Id')
            ?? $request->input('user');

        return [
            'end_user_id' => $this->stringOrNull($id),
            'end_user_name' => $this->stringOrNull($meta['end_user_name'] ?? $request->header('X-End-User-Name')),
            'end_user_email' => $this->stringOrNull($meta['end_user_email'] ?? $request->header('X-End-User-Email')),
            'feature' => $this->stringOrNull($meta['feature'] ?? $request->header('X-Client-Feature')),
            'session_id' => $this->stringOrNull($meta['session_id'] ?? $request->header('X-Client-Session-Id')),
        ];
    }

    private function logRejection(
        AiApplication $application,
        Request $request,
        string $requestId,
        array $actor,
        $startedAt,
        AiGatewayException $e
    ): void {
        try {
            AiRequest::create([
                'request_id' => $requestId,
                'ai_application_id' => $application->id,
                'model' => (string) $request->input('model', ''),
                'stream' => $request->boolean('stream'),
                'status' => AiRequest::STATUS_ERROR,
                'http_status' => $e->status,
                'latency_ms' => $this->latencyMs($startedAt),
                'error_code' => $e->errorCode,
                'error_message' => $e->getMessage(),
                'end_user_id' => $actor['end_user_id'],
                'end_user_name' => $actor['end_user_name'],
                'end_user_email' => $actor['end_user_email'],
                'feature' => $actor['feature'],
                'session_id' => $actor['session_id'],
                'started_at' => $startedAt,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $ignored) {
            // Rejection logging must never mask the original error.
        }
    }

    private function latencyMs($startedAt): int
    {
        return (int) max(0, round($startedAt->floatDiffInSeconds(now()) * 1000));
    }

    private function stringOrNull($value): ?string
    {
        $value = $this->scrubScalar($value);

        return $value === null ? null : mb_substr($value, 0, 191);
    }
}
