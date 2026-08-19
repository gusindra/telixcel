<?php

namespace App\Http\Livewire\Ai\Concerns;

use App\Models\AiApplication;
use App\Models\AiModel;
use App\Models\AiRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait BuildsUsageTree
{
    private function buildGraph($byModel, $requests, $usage): array
    {
        $reqStats = (clone $requests)
            ->selectRaw('model, count(*) as reqs, sum(case when status = ? then 1 else 0 end) as ok, avg(latency_ms) as latency, max(created_at) as last_at', [AiRequest::STATUS_SUCCESS])
            ->groupBy('model')
            ->get()
            ->keyBy('model');

        $catalogIds = $byModel->pluck('model')->filter()->values()->all();
        $catalog = $catalogIds === []
            ? collect()
            : AiModel::query()->whereIn('model_identifier', $catalogIds)->get()->keyBy('model_identifier');

        $models = [];
        $providers = [];

        foreach ($byModel as $row) {
            $meta = $this->providerOf((string) $row->model, $catalog->get($row->model));
            $stats = $reqStats->get($row->model);
            $reqs = max((int) $row->usage_rows, $stats ? (int) $stats->reqs : 0);
            $ok = $stats ? (int) $stats->ok : 0;
            $last = $row->last_at ?: ($stats->last_at ?? null);

            $models[] = [
                'id' => (string) $row->model,
                'name' => Str::afterLast((string) $row->model, '/'),
                'full' => (string) $row->model,
                'provider' => $meta['key'],
                'provider_label' => $meta['label'],
                'requests' => $reqs,
                'tokens' => (int) $row->total_tokens,
                'input_tokens' => (int) $row->input_tokens,
                'output_tokens' => (int) $row->output_tokens,
                'cost' => (float) $row->cost,
                'latency' => $stats && $stats->latency !== null ? (int) round((float) $stats->latency) : null,
                'success' => $stats && (int) $stats->reqs > 0 ? round(100 * $ok / (int) $stats->reqs, 1) : null,
                'last_at' => $last ? Carbon::parse($last) : null,
                'status' => $reqs > 0 ? 'live' : 'idle',
                'ghost' => false,
                'type' => 'model',
                'type_label' => 'Model',
            ];

            if (! isset($providers[$meta['key']])) {
                $providers[$meta['key']] = [
                    'id' => $meta['key'],
                    'name' => $meta['label'],
                    'requests' => 0,
                    'tokens' => 0,
                    'cost' => 0.0,
                    'models' => [],
                    'status' => 'idle',
                    'ghost' => false,
                    'type' => 'provider',
                    'type_label' => 'Provider',
                ];
            }
            $providers[$meta['key']]['requests'] += $reqs;
            $providers[$meta['key']]['tokens'] += (int) $row->total_tokens;
            $providers[$meta['key']]['cost'] += (float) $row->cost;
            $providers[$meta['key']]['models'][] = (string) $row->model;
            if ($reqs > 0) {
                $providers[$meta['key']]['status'] = 'live';
            }
        }

        $providers = array_values($providers);
        $appNodes = $this->applicationNodes($usage, $requests);
        $links = $this->applicationLinks($usage, $catalog);

        $totalReq = (int) array_sum(array_column($appNodes, 'requests'));
        $totalTok = (int) array_sum(array_column($appNodes, 'tokens'));
        $totalCost = (float) array_sum(array_column($appNodes, 'cost'));
        if ($totalReq === 0) {
            $totalReq = (int) array_sum(array_column($models, 'requests'));
            $totalTok = (int) array_sum(array_column($models, 'tokens'));
            $totalCost = (float) array_sum(array_column($models, 'cost'));
        }
        $peak = max(1, $totalReq, (int) array_sum(array_column($models, 'requests')));

        $gateway = [
            'id' => 'gateway',
            'name' => 'Telixcel Gateway',
            'requests' => $totalReq,
            'tokens' => $totalTok,
            'cost' => $totalCost,
            'status' => $totalReq > 0 ? 'routing' : 'idle',
            'ghost' => false,
            'type' => 'routing',
            'type_label' => 'AI routing',
        ];

        if ($providers === []) {
            $providers[] = [
                'id' => '_empty_provider',
                'name' => 'Awaiting traffic',
                'requests' => 0,
                'tokens' => 0,
                'cost' => 0.0,
                'models' => [],
                'status' => 'idle',
                'ghost' => true,
                'type' => 'provider',
                'type_label' => 'Provider',
            ];
        }
        if ($models === []) {
            $models[] = [
                'id' => '_empty_model',
                'name' => 'No model traffic',
                'full' => '',
                'provider' => '_empty_provider',
                'provider_label' => '',
                'requests' => 0,
                'tokens' => 0,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'cost' => 0.0,
                'latency' => null,
                'success' => null,
                'last_at' => null,
                'status' => 'idle',
                'ghost' => true,
                'type' => 'model',
                'type_label' => 'Model',
            ];
        }

        $laid = $this->layoutTree($appNodes, $gateway, $providers, $models, $peak);
        $appNodes = $laid['apps'];
        $gateway = $laid['gateway'];
        $providers = $laid['providers'];
        $models = $laid['models'];

        $edges = [];
        foreach ($appNodes as $app) {
            if (! empty($app['ghost'])) {
                continue;
            }
            $edges[] = $this->makeEdge($app, $gateway, (int) $app['requests'], (int) $app['tokens'], (float) $app['cost'], 'e-'.$app['id'].'-gw', $peak);
        }
        foreach ($providers as $provider) {
            if (! empty($provider['ghost'])) {
                continue;
            }
            $edges[] = $this->makeEdge($gateway, $provider, (int) $provider['requests'], (int) $provider['tokens'], (float) $provider['cost'], 'e-gw-'.$provider['id'], $peak);
            foreach ($models as $model) {
                if (! empty($model['ghost']) || $model['provider'] !== $provider['id']) {
                    continue;
                }
                $edges[] = $this->makeEdge($provider, $model, (int) $model['requests'], (int) $model['tokens'], (float) $model['cost'], 'e-m-'.md5($model['id']), $peak);
            }
        }

        return [
            'w' => $laid['w'],
            'h' => $laid['h'],
            'apps' => $appNodes,
            'gateway' => $gateway,
            'providers' => $providers,
            'models' => $models,
            'edges' => $edges,
            'links' => $links,
            'lanes' => [
                ['key' => 'application', 'index' => '01', 'label' => 'Applications'],
                ['key' => 'routing', 'index' => '02', 'label' => 'AI routing'],
                ['key' => 'provider', 'index' => '03', 'label' => 'Providers'],
                ['key' => 'model', 'index' => '04', 'label' => 'Models'],
            ],
        ];
    }

    private function applicationNodes($usage, $requests): array
    {
        $scoped = $this->scopedApplication();
        $apps = $scoped
            ? collect([$scoped])
            : AiApplication::query()->orderBy('name')->limit(24)->get();

        $usageByApp = (clone $usage)
            ->selectRaw('ai_application_id, count(*) as usage_rows, coalesce(sum(total_tokens),0) as total_tokens, coalesce(sum(cost),0) as cost')
            ->groupBy('ai_application_id')
            ->get()
            ->keyBy('ai_application_id');

        $reqByApp = (clone $requests)
            ->selectRaw('ai_application_id, count(*) as reqs')
            ->groupBy('ai_application_id')
            ->get()
            ->keyBy('ai_application_id');

        $nodes = [];
        foreach ($apps as $app) {
            $u = $usageByApp->get($app->id);
            $r = $reqByApp->get($app->id);
            $reqs = max($u ? (int) $u->usage_rows : 0, $r ? (int) $r->reqs : 0);
            $nodes[] = [
                'id' => 'app-'.$app->id,
                'name' => $app->name,
                'requests' => $reqs,
                'tokens' => $u ? (int) $u->total_tokens : 0,
                'cost' => $u ? (float) $u->cost : 0.0,
                'status' => $reqs > 0 ? 'live' : 'idle',
                'ghost' => false,
                'type' => 'application',
                'type_label' => 'Application',
            ];
        }

        if ($nodes === []) {
            $nodes[] = [
                'id' => '_empty_app',
                'name' => 'No applications',
                'requests' => 0,
                'tokens' => 0,
                'cost' => 0.0,
                'status' => 'idle',
                'ghost' => true,
                'type' => 'application',
                'type_label' => 'Application',
            ];
        }

        return $nodes;
    }

    private function applicationLinks($usage, $catalog): array
    {
        $rows = (clone $usage)
            ->selectRaw('ai_application_id, model')
            ->groupBy('ai_application_id', 'model')
            ->get();

        $links = [];
        foreach ($rows as $row) {
            $appId = 'app-'.$row->ai_application_id;
            $meta = $this->providerOf((string) $row->model, $catalog->get($row->model));
            if (! isset($links[$appId])) {
                $links[$appId] = ['providers' => [], 'models' => []];
            }
            $links[$appId]['models'][] = (string) $row->model;
            $links[$appId]['providers'][] = $meta['key'];
        }

        foreach ($links as $appId => $link) {
            $links[$appId]['models'] = array_values(array_unique($link['models']));
            $links[$appId]['providers'] = array_values(array_unique($link['providers']));
        }

        return $links;
    }

    private function stackY(int $index, int $count, int $height, int $card = 68, int $gap = 16): float
    {
        $group = ($count * $card) + (max(0, $count - 1) * $gap);
        $start = ($height - $group) / 2;

        return $start + ($card / 2) + ($index * ($card + $gap));
    }

    /**
     * @return array{apps:array,gateway:array,providers:array,models:array,w:int,h:int}
     */
    private function layoutTree(array $apps, array $gateway, array $providers, array $models, int $peak): array
    {
        $nw = 184;
        $nh = 58;
        $colGap = 88;
        $rowGap = 16;
        $groupGap = 22;
        $pad = 56;
        $half = $nw / 2;

        $xs = [
            $pad + $half,
            $pad + $nw + $colGap + $half,
            $pad + (2 * ($nw + $colGap)) + $half,
            $pad + (3 * ($nw + $colGap)) + $half,
        ];

        $byProvider = [];
        foreach ($models as $model) {
            $byProvider[$model['provider']][] = $model;
        }

        $placedModels = [];
        $cursor = $pad + ($nh / 2);

        foreach ($providers as $i => $provider) {
            $kids = $byProvider[$provider['id']] ?? [];
            if ($kids === []) {
                $providers[$i]['x'] = $xs[2];
                $providers[$i]['y'] = $cursor;
                $providers[$i]['share'] = $providers[$i]['requests'] / $peak;
                $cursor += $nh + $groupGap;
                continue;
            }

            $first = $cursor;
            foreach ($kids as $kid) {
                $kid['x'] = $xs[3];
                $kid['y'] = $cursor;
                $kid['share'] = $kid['requests'] / $peak;
                $placedModels[] = $kid;
                $cursor += $nh + $rowGap;
            }
            $last = $cursor - $nh - $rowGap;
            $providers[$i]['x'] = $xs[2];
            $providers[$i]['y'] = ($first + $last) / 2;
            $providers[$i]['share'] = $providers[$i]['requests'] / $peak;
            $cursor += $groupGap;
        }

        $models = $placedModels !== [] ? $placedModels : $models;
        foreach ($models as $i => $model) {
            if (! isset($models[$i]['x'])) {
                $models[$i]['x'] = $xs[3];
                $models[$i]['y'] = $this->stackY($i, count($models), 320, $nh, $rowGap);
            }
            $models[$i]['share'] = ($models[$i]['requests'] ?? 0) / $peak;
        }

        $provYs = array_column($providers, 'y') ?: [$pad + 80];
        $gateway['x'] = $xs[1];
        $gateway['y'] = array_sum($provYs) / count($provYs);
        $gateway['share'] = ($gateway['requests'] ?? 0) / $peak;

        $appCount = max(1, count($apps));
        $appSpan = ($appCount * $nh) + (max(0, $appCount - 1) * $rowGap);
        $appStart = $gateway['y'] - ($appSpan / 2) + ($nh / 2);
        foreach ($apps as $i => $app) {
            $apps[$i]['x'] = $xs[0];
            $apps[$i]['y'] = $appStart + ($i * ($nh + $rowGap));
            $apps[$i]['share'] = ($apps[$i]['requests'] ?? 0) / $peak;
        }

        $ys = array_merge(
            array_column($apps, 'y'),
            [$gateway['y']],
            array_column($providers, 'y'),
            array_column($models, 'y')
        );
        $maxY = $ys === [] ? 280 : max($ys);

        return [
            'apps' => $apps,
            'gateway' => $gateway,
            'providers' => $providers,
            'models' => $models,
            'w' => max(760, (int) ceil($xs[3] + $half + $pad)),
            'h' => max(320, (int) ceil($maxY + ($nh / 2) + $pad)),
        ];
    }

    private function makeEdge(array $from, array $to, int $requests, int $tokens, float $cost, string $id, int $peak): array
    {
        $x1 = $from['x'] + 92;
        $y1 = $from['y'];
        $x2 = $to['x'] - 92;
        $y2 = $to['y'];
        $cx = ($x1 + $x2) / 2;
        $share = $requests / max(1, $peak);

        return [
            'id' => $id,
            'from' => $from['id'],
            'to' => $to['id'],
            'd' => 'M '.$x1.' '.$y1.' C '.$cx.' '.$y1.', '.$cx.' '.$y2.', '.$x2.' '.$y2,
            'requests' => $requests,
            'tokens' => $tokens,
            'cost' => $cost,
            'mx' => ($x1 + $x2) / 2,
            'my' => ($y1 + $y2) / 2,
            'ex' => $x2,
            'ey' => $y2,
            'width' => round(1.4 + (1.8 * $share), 2),
            'duration' => round(2.8 - (1.1 * $share), 2),
        ];
    }

    private function providerOf(string $model, ?AiModel $catalog = null): array
    {
        $key = $catalog && $catalog->provider
            ? strtolower((string) $catalog->provider)
            : (Str::contains($model, '/') ? strtolower((string) Str::before($model, '/')) : 'upstream');

        $labels = (array) config('ai.providers', []);

        return [
            'key' => $key !== '' ? $key : 'upstream',
            'label' => $labels[$key] ?? Str::title($key),
        ];
    }

    private function litIds(array $graph): array
    {
        $ids = $this->focusIds($graph);
        $query = strtolower(trim((string) $this->graphQuery));
        if ($query === '') {
            return $ids;
        }

        $matched = ['gateway'];
        foreach ($graph['apps'] as $app) {
            if (Str::contains(strtolower($app['name']), $query)) {
                $matched[] = $app['id'];
                $matched = array_merge($matched, $graph['links'][$app['id']]['providers'] ?? []);
                $matched = array_merge($matched, $graph['links'][$app['id']]['models'] ?? []);
            }
        }
        foreach ($graph['providers'] as $provider) {
            if (Str::contains(strtolower($provider['name'].' '.$provider['id']), $query)) {
                $matched[] = $provider['id'];
                foreach ($graph['models'] as $model) {
                    if ($model['provider'] === $provider['id']) {
                        $matched[] = $model['id'];
                    }
                }
            }
        }
        foreach ($graph['models'] as $model) {
            if (Str::contains(strtolower(($model['full'] ?? '').' '.($model['name'] ?? '')), $query)) {
                $matched[] = $model['id'];
                $matched[] = $model['provider'];
            }
        }

        return $ids === ['*'] ? $matched : array_values(array_intersect($ids, $matched));
    }

    private function focusIds(array $graph): array
    {
        if ($this->focus === '') {
            return ['*'];
        }

        [$layer, $key] = array_pad(explode('|', (string) $this->focus, 2), 2, '');
        $ids = ['gateway'];

        if ($layer === 'routing') {
            foreach ($graph['apps'] as $app) {
                $ids[] = $app['id'];
            }
            foreach ($graph['providers'] as $provider) {
                $ids[] = $provider['id'];
            }
            foreach ($graph['models'] as $model) {
                $ids[] = $model['id'];
            }

            return $ids;
        }

        if ($layer === 'application') {
            $ids[] = $key;
            $ids = array_merge($ids, $graph['links'][$key]['providers'] ?? []);
            $ids = array_merge($ids, $graph['links'][$key]['models'] ?? []);

            return $ids;
        }

        if ($layer === 'provider') {
            $ids[] = $key;
            foreach ($graph['links'] as $appId => $link) {
                if (in_array($key, $link['providers'], true)) {
                    $ids[] = $appId;
                }
            }
            foreach ($graph['models'] as $model) {
                if ($model['provider'] === $key) {
                    $ids[] = $model['id'];
                }
            }

            return $ids;
        }

        if ($layer === 'model') {
            $ids[] = $key;
            foreach ($graph['models'] as $model) {
                if ($model['id'] === $key) {
                    $ids[] = $model['provider'];
                }
            }
            foreach ($graph['links'] as $appId => $link) {
                if (in_array($key, $link['models'], true)) {
                    $ids[] = $appId;
                }
            }
        }

        return $ids;
    }

    private function inspectedModel(array $graph): ?array
    {
        if ($this->inspectModel === '') {
            return null;
        }

        foreach ($graph['models'] as $model) {
            if ($model['id'] === $this->inspectModel && empty($model['ghost'])) {
                return $model;
            }
        }

        return null;
    }
}
