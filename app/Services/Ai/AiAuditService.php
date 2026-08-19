<?php

namespace App\Services\Ai;

use App\Models\LogChange;
use Illuminate\Support\Facades\Log;

class AiAuditService
{
    public function record(string $model, $modelId, string $remark, $before = null): void
    {
        try {
            LogChange::create([
                'model' => $model,
                'model_id' => $modelId,
                'before' => is_array($before) ? $this->redact($before) : $before,
                'remark' => $remark,
            ]);
        } catch (\Throwable $e) {
            Log::warning('AI audit log failed', ['model' => $model, 'model_id' => $modelId]);
        }
    }

    /**
     * @param  array<string,mixed>  $payload
     * @return array<string,mixed>
     */
    public function redact(array $payload): array
    {
        $blocked = ['api_key', 'api_key_hash', 'api_key_encrypted', 'authorization', 'password'];

        foreach ($payload as $key => $value) {
            if (in_array(strtolower((string) $key), $blocked, true)) {
                $payload[$key] = '[redacted]';
            }
        }

        return $payload;
    }
}
