<?php

namespace App\Jobs\Ai;

use App\Services\Ai\AiUsageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAiUsage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array<string,mixed> */
    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $this->withoutSecrets($payload);
    }

    public function handle(AiUsageService $usage): void
    {
        $usage->record($this->payload);
    }

    /**
     * @param  array<string,mixed>  $payload
     * @return array<string,mixed>
     */
    private function withoutSecrets(array $payload): array
    {
        unset($payload['api_key'], $payload['authorization'], $payload['messages'], $payload['prompt'], $payload['response']);

        return $payload;
    }

    public function failed(\Throwable $e): void
    {
        Log::warning('AI usage job failed', [
            'request_id' => $this->payload['request_id'] ?? null,
        ]);
    }
}
