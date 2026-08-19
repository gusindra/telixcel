<?php

namespace App\Services\Ai;

use App\Models\AiApplication;
use App\Models\AiModel;
use App\Models\AiRequest;
use App\Models\AiUsage;

class AiUsageService
{
    public function __construct(private AiQuotaService $quota)
    {
    }

    /**
     * @param  array<string,mixed>  $payload
     */
    public function record(array $payload): ?AiUsage
    {
        /** @var AiRequest|null $request */
        $request = AiRequest::query()->where('request_id', $payload['request_id'] ?? '')->first();

        $input = $this->nullableInt($payload['input_tokens'] ?? null);
        $output = $this->nullableInt($payload['output_tokens'] ?? null);
        $total = $this->nullableInt($payload['total_tokens'] ?? null);

        if ($total === null && ($input !== null || $output !== null)) {
            $total = (int) $input + (int) $output;
        }

        $model = isset($payload['model'])
            ? AiModel::query()->where('model_identifier', $payload['model'])->first()
            : null;

        $cost = $this->resolveCost($payload['cost'] ?? null, $input, $output, $model);
        $currency = $payload['currency'] ?? ($model->currency ?? 'USD');

        $usage = AiUsage::create([
            'ai_application_id' => $payload['ai_application_id'],
            'ai_request_id' => $request?->id,
            'model' => $payload['model'] ?? '',
            'input_tokens' => $input,
            'output_tokens' => $output,
            'total_tokens' => $total,
            'cost' => $cost,
            'currency' => $currency,
            'end_user_id' => $payload['end_user_id'] ?? $request?->end_user_id,
            'end_user_name' => $payload['end_user_name'] ?? $request?->end_user_name,
            'feature' => $payload['feature'] ?? $request?->feature,
        ]);

        if ($request) {
            $request->fill([
                'status' => $payload['status'] ?? AiRequest::STATUS_SUCCESS,
                'http_status' => $payload['http_status'] ?? 200,
                'latency_ms' => $payload['latency_ms'] ?? $request->latency_ms,
                'error_code' => $payload['error_code'] ?? null,
                'error_message' => $payload['error_message'] ?? null,
                'completed_at' => $payload['completed_at'] ?? now(),
            ])->save();
        }

        $application = AiApplication::query()->find($payload['ai_application_id']);
        if ($application) {
            $this->quota->increment($application, $total, $cost);
        }

        return $usage;
    }

    /**
     * Extract usage from an OpenAI-compatible payload (JSON or last SSE object).
     *
     * @param  array<string,mixed>  $body
     * @return array{input_tokens:?int,output_tokens:?int,total_tokens:?int,cost:?float,currency:?string}
     */
    public function extractFromUpstream(array $body): array
    {
        $usage = $body['usage'] ?? [];
        if (! is_array($usage)) {
            $usage = [];
        }

        $input = $this->nullableInt($usage['prompt_tokens'] ?? $usage['input_tokens'] ?? null);
        $output = $this->nullableInt($usage['completion_tokens'] ?? $usage['output_tokens'] ?? null);
        $total = $this->nullableInt($usage['total_tokens'] ?? null);
        $cost = $this->nullableFloat(
            $usage['cost']
            ?? $usage['total_cost']
            ?? $usage['cost_usd']
            ?? $usage['estimated_cost']
            ?? data_get($usage, 'costs.total')
            ?? $body['cost']
            ?? null
        );

        return [
            'input_tokens' => $input,
            'output_tokens' => $output,
            'total_tokens' => $total,
            'cost' => $cost,
            'currency' => $usage['currency'] ?? $body['currency'] ?? null,
        ];
    }

    /**
     * Pull the last JSON object that contains usage from an SSE buffer.
     */
    public function extractFromSse(string $buffer): array
    {
        $found = [
            'input_tokens' => null,
            'output_tokens' => null,
            'total_tokens' => null,
            'cost' => null,
            'currency' => null,
        ];

        foreach (preg_split("/\r\n|\n|\r/", $buffer) as $line) {
            $line = trim($line);
            if (! str_starts_with($line, 'data:')) {
                continue;
            }
            $data = trim(substr($line, 5));
            if ($data === '' || $data === '[DONE]') {
                continue;
            }
            $json = json_decode($data, true);
            if (! is_array($json)) {
                continue;
            }
            $extracted = $this->extractFromUpstream($json);
            if ($extracted['input_tokens'] !== null || $extracted['output_tokens'] !== null || $extracted['cost'] !== null) {
                $found = $extracted;
            }
        }

        return $found;
    }

    public function estimateCost(?int $input, ?int $output, ?AiModel $model): ?float
    {
        return $this->resolveCost(null, $input, $output, $model);
    }

    private function resolveCost($upstreamCost, ?int $input, ?int $output, ?AiModel $model): ?float
    {
        $explicit = $this->nullableFloat($upstreamCost);
        if ($explicit !== null) {
            return $explicit;
        }

        if (! $model || ! $model->hasPricing()) {
            return null;
        }

        if ($input === null && $output === null) {
            return null;
        }

        $cost = 0.0;
        if ($input !== null && $model->input_price_per_million !== null) {
            $cost += ($input / 1000000) * (float) $model->input_price_per_million;
        }
        if ($output !== null && $model->output_price_per_million !== null) {
            $cost += ($output / 1000000) * (float) $model->output_price_per_million;
        }

        return $cost;
    }

    private function nullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
