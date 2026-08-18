<?php

namespace App\Services\Ai;

use App\Exceptions\AiGatewayException;
use App\Models\AiApplication;
use App\Models\AiUsage;
use Illuminate\Support\Facades\Cache;

class AiQuotaService
{
    public function assertWithinLimits(AiApplication $application, ?string $requestId = null): void
    {
        if ($application->monthly_cost_limit === null) {
            return;
        }

        $used = $application->usdToLimitCurrency($this->usedCost($application));
        if ($used >= (float) $application->monthly_cost_limit) {
            throw AiGatewayException::quotaExceeded('cost', $requestId);
        }
    }

    public function usedTokens(AiApplication $application): int
    {
        return (int) $this->remember($application, 'tokens', function () use ($application) {
            return (int) AiUsage::query()
                ->where('ai_application_id', $application->id)
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('total_tokens');
        });
    }

    public function usedCost(AiApplication $application): float
    {
        return (float) $this->remember($application, 'cost', function () use ($application) {
            return (float) AiUsage::query()
                ->where('ai_application_id', $application->id)
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('cost');
        });
    }

    public function increment(AiApplication $application, ?int $tokens, ?float $cost): void
    {
        if ($tokens !== null && $tokens > 0) {
            $this->bump($application, 'tokens', $tokens);
        }

        if ($cost !== null && $cost > 0) {
            $key = $this->key($application, 'cost');
            if (! Cache::has($key)) {
                $this->usedCost($application);
            }
            $current = (float) Cache::get($key, 0);
            Cache::put($key, $current + $cost, now()->endOfMonth());
        }
    }

    private function bump(AiApplication $application, string $type, int $amount): void
    {
        $key = $this->key($application, $type);
        if (! Cache::has($key)) {
            $this->usedTokens($application);
        }
        Cache::increment($key, $amount);
    }

    private function remember(AiApplication $application, string $type, callable $resolver)
    {
        $key = $this->key($application, $type);

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $value = $resolver();
        Cache::put($key, $value, now()->endOfMonth());

        return $value;
    }

    private function key(AiApplication $application, string $type): string
    {
        return sprintf('ai:quota:%d:%s:%s', $application->id, now()->format('Y-m'), $type);
    }
}
