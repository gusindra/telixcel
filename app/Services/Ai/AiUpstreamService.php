<?php

namespace App\Services\Ai;

use App\Exceptions\AiGatewayException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiUpstreamService
{
    public function chat(array $payload): array
    {
        $response = $this->send($payload, false);

        return $response->json() ?? [];
    }

    /**
     * @param  array<int,string>  $identifiers
     * @return array<string,bool>
     */
    public function probeMany(array $identifiers): array
    {
        $identifiers = array_values(array_unique(array_filter($identifiers)));
        $results = array_fill_keys($identifiers, false);
        if ($identifiers === []) {
            return $results;
        }

        $url = $this->completionsUrl();
        $timeout = max(3, (int) config('ai.warmup_timeout', 8));

        foreach (array_chunk($identifiers, 5) as $chunk) {
            $responses = Http::pool(function (Pool $pool) use ($chunk, $url, $timeout) {
                $requests = [];
                foreach ($chunk as $id) {
                    $requests[$id] = $pool->as($id)
                        ->acceptJson()
                        ->asJson()
                        ->timeout($timeout)
                        ->withHeaders([
                            'Authorization' => 'Bearer '.(string) config('ai.api_key'),
                        ])
                        ->post($url, [
                            'model' => $id,
                            'messages' => [
                                ['role' => 'user', 'content' => 'ping'],
                            ],
                            'max_tokens' => 8,
                        ]);
                }

                return $requests;
            });

            foreach ($chunk as $id) {
                $results[$id] = $this->responseLooksUsable($responses[$id] ?? null);
            }
        }

        return $results;
    }

    /**
     * @return array<int,array{id:string,name:?string,owned_by:?string}>
     */
    public function listModels(): array
    {
        $url = $this->modelsUrl();

        try {
            $response = $this->client(false)->get($url);
        } catch (ConnectionException $e) {
            Log::warning('AI gateway models list failed', ['url' => $url]);

            throw AiGatewayException::upstreamUnavailable();
        }

        if (! $response->successful()) {
            throw AiGatewayException::upstreamError('Could not list models from the provider.');
        }

        $data = $response->json('data');
        if (! is_array($data)) {
            return [];
        }

        $models = [];
        foreach ($data as $row) {
            if (! is_array($row)) {
                continue;
            }
            $id = $row['id'] ?? null;
            if (! is_string($id) || $id === '') {
                continue;
            }
            $name = $row['name'] ?? $row['display_name'] ?? null;
            $ownedBy = $row['owned_by'] ?? $row['owned_by_name'] ?? $row['provider'] ?? null;
            $models[] = [
                'id' => $id,
                'name' => is_string($name) && $name !== '' ? $name : null,
                'owned_by' => is_string($ownedBy) && $ownedBy !== '' ? $ownedBy : null,
            ];
        }

        return $models;
    }

    /**
     * Yield raw upstream chunks without buffering the full body.
     *
     * @return \Generator<int,string>
     */
    public function stream(array $payload): \Generator
    {
        $response = $this->send($payload, true);
        $body = $response->toPsrResponse()->getBody();

        while (! $body->eof()) {
            $chunk = $body->read(1024);
            if ($chunk !== '') {
                yield $chunk;
            }
        }
    }

    private function send(array $payload, bool $stream): Response
    {
        $url = $this->completionsUrl();

        try {
            $response = $this->client($stream)->post($url, $payload);
        } catch (ConnectionException $e) {
            $message = strtolower($e->getMessage());
            if (str_contains($message, 'timed out') || str_contains($message, 'timeout')) {
                throw AiGatewayException::upstreamTimeout();
            }

            Log::warning('AI gateway upstream connection failed', [
                'url' => $url,
            ]);

            throw AiGatewayException::upstreamUnavailable();
        }

        $this->throwIfFailed($response);

        return $response;
    }

    private function client(bool $stream): PendingRequest
    {
        $request = Http::acceptJson()
            ->asJson()
            ->timeout((int) config('ai.timeout', 180))
            ->withHeaders([
                'Authorization' => 'Bearer '.(string) config('ai.api_key'),
            ]);

        if ($stream) {
            $request = $request->withOptions(['stream' => true]);
        }

        return $request;
    }

    private function throwIfFailed(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();
        $code = data_get($response->json(), 'error.code');
        $message = $this->sanitize(data_get($response->json(), 'error.message'));

        if ($status === 408 || $status === 504) {
            throw AiGatewayException::upstreamTimeout();
        }

        if (in_array($status, [401, 403], true) && is_string($code) && strtoupper($code) === 'MODEL_NOT_FOUND') {
            throw AiGatewayException::modelNotFound((string) data_get($response->json(), 'error.model', 'unknown'));
        }

        if ($status === 404 || (is_string($code) && strtoupper((string) $code) === 'MODEL_NOT_FOUND')) {
            throw AiGatewayException::modelNotFound((string) data_get($response->json(), 'error.model', 'unknown'));
        }

        if ($status === 429) {
            throw AiGatewayException::rateLimited();
        }

        if ($status >= 500) {
            throw AiGatewayException::upstreamUnavailable();
        }

        throw AiGatewayException::upstreamError($message ?: 'The AI provider returned an error.');
    }

    private function responseLooksUsable($response): bool
    {
        if (! $response instanceof Response || ! $response->successful()) {
            return false;
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        return is_string($content) && trim($content) !== '';
    }

    private function sanitize($message): string
    {
        if (! is_string($message) || $message === '') {
            return 'The AI provider returned an error.';
        }

        if (preg_match('/api[_-]?key|bearer\s|sk-|authorization/i', $message)) {
            return 'The AI provider returned an error.';
        }

        return $message;
    }

    public function completionsUrl(): string
    {
        $base = rtrim((string) config('ai.base_url'), '/');

        if ($base === '') {
            throw AiGatewayException::internal();
        }

        return $base.'/chat/completions';
    }

    public function modelsUrl(): string
    {
        $base = rtrim((string) config('ai.base_url'), '/');

        if ($base === '') {
            throw AiGatewayException::internal();
        }

        return $base.'/models';
    }
}
