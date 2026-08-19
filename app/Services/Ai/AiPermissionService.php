<?php

namespace App\Services\Ai;

use App\Exceptions\AiGatewayException;
use App\Models\AiApplication;
use App\Models\AiModel;
use Illuminate\Support\Facades\Cache;

class AiPermissionService
{
    public function assertAllowed(AiApplication $application, AiModel $model, ?string $requestId = null): void
    {
        $allowed = $this->allowedModelIds($application);

        // Empty allow-list means the application may use any upstream model.
        if ($allowed === []) {
            return;
        }

        if (! in_array($model->id, $allowed, true)) {
            throw AiGatewayException::modelNotAllowed($requestId);
        }
    }

    /**
     * @return array<int,int>
     */
    public function allowedModelIds(AiApplication $application): array
    {
        $ttl = (int) config('ai.cache.permission_ttl', 60);
        $key = 'ai:perms:'.$application->id;

        return Cache::remember($key, $ttl, function () use ($application) {
            return $application->models()->pluck('ai_models.id')->map(fn ($id) => (int) $id)->all();
        });
    }

    public function forget(AiApplication $application): void
    {
        Cache::forget('ai:perms:'.$application->id);
    }
}
