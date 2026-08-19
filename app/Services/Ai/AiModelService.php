<?php

namespace App\Services\Ai;

use App\Exceptions\AiGatewayException;
use App\Models\AiModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AiModelService
{
    public function resolveEnabled(string $identifier, ?string $requestId = null): AiModel
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            throw AiGatewayException::modelNotFound($identifier, $requestId);
        }

        $ttl = (int) config('ai.cache.model_ttl', 60);
        $key = 'ai:model:'.$identifier;

        $model = Cache::remember($key, $ttl, function () use ($identifier) {
            return $this->findOrCreate($identifier);
        });
        if ($model && ! $model->hasPricing()) {
            $this->applyDefaultPrices($model);
        }

        if (! $model || ! $model->isUsable()) {
            throw AiGatewayException::modelNotFound($identifier, $requestId);
        }

        return $model;
    }

    /**
     * One enabled model per public family, preferred source first.
     *
     * @param  array<int,int>  $allowedIds
     * @return Collection<int,AiModel>
     */
    public function publicCatalog(array $allowedIds = []): Collection
    {
        $query = AiModel::query()->usable()->orderBy('model_identifier');
        if ($allowedIds !== []) {
            $query->whereIn('id', $allowedIds);
        }

        return $this->collapseByFamily($query->get());
    }

    /**
     * Enabled sources for a public model or concrete upstream id, in fallback order.
     *
     * @param  array<int,int>  $allowedIds
     * @return Collection<int,AiModel>
     */
    public function resolveChain(string $identifier, array $allowedIds = [], ?string $requestId = null): Collection
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            throw AiGatewayException::modelNotFound($identifier, $requestId);
        }

        $family = AiModel::familyKeyOf($identifier);
        $query = AiModel::query()->usable();
        if ($allowedIds !== []) {
            $query->whereIn('id', $allowedIds);
        }

        $candidates = $query->get()->filter(function (AiModel $model) use ($identifier, $family) {
            return $model->model_identifier === $identifier
                || ($family !== '' && $model->familyKey() === $family);
        })->values();

        if ($candidates->isEmpty()) {
            if ($allowedIds === [] && str_contains($identifier, '/')) {
                return collect([$this->resolveEnabled($identifier, $requestId)]);
            }

            $exact = AiModel::query()->where('model_identifier', $identifier)->first();
            if ($exact && $exact->isUsable()) {
                throw AiGatewayException::modelNotAllowed($requestId);
            }

            throw AiGatewayException::modelNotFound($identifier, $requestId);
        }

        $sorted = collect($this->sortByPriority($candidates->all()));

        if (! config('ai.fallback.enabled', true)) {
            $exact = $sorted->firstWhere('model_identifier', $identifier);

            return collect([$exact ?: $sorted->first()]);
        }

        return $sorted->values();
    }

    /**
     * @param  Collection<int,AiModel>|array<int,AiModel>  $models
     * @return Collection<int,AiModel>
     */
    public function collapseByFamily($models): Collection
    {
        $grouped = [];
        foreach ($models as $model) {
            $key = $model->familyKey();
            if ($key === '') {
                $key = (string) $model->model_identifier;
            }
            $grouped[$key][] = $model;
        }

        $primaries = [];
        foreach ($grouped as $rows) {
            $sorted = $this->sortByPriority($rows);
            $primaries[] = $sorted[0];
        }

        usort($primaries, function (AiModel $a, AiModel $b) {
            return strcmp((string) $a->model_identifier, (string) $b->model_identifier);
        });

        return collect($primaries)->values();
    }

    /**
     * @param  array<int,AiModel>  $models
     * @return array<int,AiModel>
     */
    public function sortByPriority(array $models): array
    {
        $order = [];
        foreach ((array) config('ai.fallback.providers', []) as $index => $provider) {
            $order[strtolower(trim((string) $provider))] = $index;
        }

        usort($models, function (AiModel $a, AiModel $b) use ($order) {
            $pa = $order[$a->sourceKey()] ?? 1000;
            $pb = $order[$b->sourceKey()] ?? 1000;
            if ($pa !== $pb) {
                return $pa <=> $pb;
            }

            return strcmp((string) $a->model_identifier, (string) $b->model_identifier);
        });

        return array_values($models);
    }

    private function findOrCreate(string $identifier, bool $enableNew = true): AiModel
    {
        $existing = AiModel::query()->where('model_identifier', $identifier)->first();
        if ($existing) {
            $this->applyDefaultPrices($existing);

            return $existing;
        }

        $provider = str_contains($identifier, '/')
            ? strtolower((string) strtok($identifier, '/'))
            : 'upstream';

        $prices = $this->defaultPrices($identifier);

        return AiModel::query()->create([
            'model_identifier' => $identifier,
            'display_name' => AiModel::prettyName($identifier),
            'provider' => $provider,
            'enabled' => $enableNew,
            'verified_at' => $enableNew ? now() : null,
            'currency' => 'USD',
            'input_price_per_million' => $prices['input'],
            'output_price_per_million' => $prices['output'],
        ]);
    }

    public function applyDefaultPrices(AiModel $model): void
    {
        if ($model->hasPricing()) {
            return;
        }

        $prices = $this->defaultPrices($model->model_identifier);
        $model->fill([
            'input_price_per_million' => $prices['input'],
            'output_price_per_million' => $prices['output'],
            'currency' => $model->currency ?: 'USD',
        ])->save();
    }

    /**
     * @return array{input:float,output:float}
     */
    public function defaultPrices(string $identifier): array
    {
        $id = strtolower($identifier);
        $map = (array) config('ai.model_prices', []);
        foreach ($map as $needle => $prices) {
            if ($needle === 'default' || ! is_array($prices)) {
                continue;
            }
            if (str_contains($id, (string) $needle)) {
                return [
                    'input' => (float) ($prices['input'] ?? 1),
                    'output' => (float) ($prices['output'] ?? 5),
                ];
            }
        }

        $fallback = is_array($map['default'] ?? null) ? $map['default'] : [];

        return [
            'input' => (float) ($fallback['input'] ?? 1),
            'output' => (float) ($fallback['output'] ?? 5),
        ];
    }

    /**
     * @param  array<int,array{id:string,name:?string,owned_by:?string}>  $rows
     */
    public function syncFromUpstream(array $rows): int
    {
        $seen = [];
        $count = 0;
        foreach ($rows as $row) {
            $id = trim((string) ($row['id'] ?? ''));
            if ($id === '') {
                continue;
            }
            $seen[] = $id;
            $model = $this->findOrCreate($id, false);
            $dirty = false;

            $name = isset($row['name']) && is_string($row['name']) ? trim($row['name']) : '';
            if ($name !== '' && ! str_contains($name, '/') && $model->display_name !== $name) {
                $model->display_name = $name;
                $dirty = true;
            }

            $ownedBy = isset($row['owned_by']) && is_string($row['owned_by']) ? trim($row['owned_by']) : '';
            if ($ownedBy !== '') {
                $provider = strtolower($ownedBy);
                if ($model->provider !== $provider) {
                    $model->provider = $provider;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $model->save();
            }
            $this->forget($id);
            $count++;
        }

        if ($seen !== []) {
            AiModel::query()
                ->whereNotIn('model_identifier', $seen)
                ->where('enabled', true)
                ->update([
                    'enabled' => false,
                    'verified_at' => null,
                ]);
        }

        return $count;
    }

    /**
     * Probe upstream so only models that actually answer are enabled for apps.
     *
     * @param  array<int,string>  $identifiers
     * @return array{ok:int,fail:int}
     */
    public function verifyUsable(AiUpstreamService $upstream, array $identifiers, bool $force = false): array
    {
        $identifiers = array_values(array_unique(array_filter(array_map('strval', $identifiers))));
        $ok = 0;
        $fail = 0;
        if ($identifiers === []) {
            return compact('ok', 'fail');
        }

        $ttlHours = max(1, (int) config('ai.verify_ttl_hours', 6));
        $models = AiModel::query()->whereIn('model_identifier', $identifiers)->get()->keyBy('model_identifier');
        $toProbe = [];

        foreach ($identifiers as $id) {
            $model = $models->get($id);
            if (! $model) {
                continue;
            }

            $fresh = $model->enabled
                && $model->verified_at
                && $model->verified_at->gt(now()->subHours($ttlHours));
            if ($fresh && ! $force) {
                $ok++;
                continue;
            }

            $toProbe[] = $id;
        }

        foreach ($upstream->probeMany($toProbe) as $id => $usable) {
            $model = $models->get($id);
            if (! $model) {
                continue;
            }

            $model->enabled = (bool) $usable;
            $model->verified_at = $usable ? now() : null;
            $model->save();
            $this->forget((string) $id);
            $usable ? $ok++ : $fail++;
        }

        return compact('ok', 'fail');
    }

    public function forget(string $identifier): void
    {
        Cache::forget('ai:model:'.$identifier);
    }
}
