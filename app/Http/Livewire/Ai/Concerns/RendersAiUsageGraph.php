<?php

namespace App\Http\Livewire\Ai\Concerns;

use App\Models\AiApplication;
use App\Models\AiRequest;
use App\Models\AiUsage;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait RendersAiUsageGraph
{
    use BuildsUsageTree;

    public $tab = 'overview';
    public $range = '24h';
    public $usageModel = '';
    public $endUser = '';
    public $graphQuery = '';
    public $focus = '';
    public $inspectModel = '';
    public $modelPage = 1;
    public $appPage = 1;
    public $userPage = 1;
    public $seriesMetric = 'tokens';
    public $tableMetric = 'cost';

    abstract protected function scopedApplication(): ?AiApplication;

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['overview', 'details'], true) ? $tab : 'overview';
    }

    public function setRange(string $range): void
    {
        $this->range = in_array($range, ['live', '1h', '24h', '7d', '30d'], true) ? $range : '24h';
        $this->resetTablePages();
    }

    public function updatedUsageModel(): void
    {
        $this->resetTablePages();
    }

    public function updatedEndUser(): void
    {
        $this->resetTablePages();
    }

    public function gotoModelPage(int $page): void
    {
        $this->modelPage = max(1, $page);
    }

    public function gotoAppPage(int $page): void
    {
        $this->appPage = max(1, $page);
    }

    public function gotoUserPage(int $page): void
    {
        $this->userPage = max(1, $page);
    }

    public function setSeriesMetric(string $metric): void
    {
        $this->seriesMetric = $metric === 'cost' ? 'cost' : 'tokens';
    }

    public function setTableMetric(string $metric): void
    {
        $this->tableMetric = $metric === 'tokens' ? 'tokens' : 'cost';
    }

    private function resetTablePages(): void
    {
        $this->modelPage = 1;
        $this->appPage = 1;
        $this->userPage = 1;
    }

    public function selectNode(string $layer, string $key = ''): void
    {
        $token = $layer.'|'.$key;
        if ($this->focus === $token) {
            $this->focus = '';
            $this->inspectModel = '';
            if ($layer === 'model') {
                $this->usageModel = '';
            }

            return;
        }

        $this->focus = $token;
        if ($layer === 'model') {
            $this->inspectModel = $key;
            $this->usageModel = $key;
        } else {
            $this->inspectModel = '';
            $this->usageModel = '';
        }
    }

    public function clearInspect(): void
    {
        $this->inspectModel = '';
        if (Str::startsWith((string) $this->focus, 'model|')) {
            $this->focus = '';
            $this->usageModel = '';
        }
    }

    public function formatMoney(?float $usd): string
    {
        $app = $this->scopedApplication();
        if ($app) {
            return $app->formatCost($usd);
        }

        return '$'.number_format((float) $usd, 2);
    }

    public function isLit(array $lit, string $id): bool
    {
        return in_array('*', $lit, true) || in_array($id, $lit, true);
    }

    private function slicePage($rows, int $page, int $perPage = 15): array
    {
        $rows = collect($rows);
        $total = $rows->count();
        $last = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $last));

        return [
            'rows' => $rows->forPage($page, $perPage)->values(),
            'page' => $page,
            'last' => $last,
            'total' => $total,
            'from' => $total === 0 ? 0 : (($page - 1) * $perPage) + 1,
            'to' => min($total, $page * $perPage),
        ];
    }

    private function buildSeries($usage): array
    {
        $start = $this->rangeStart();
        $now = now();
        $buckets = [];

        if (in_array($this->range, ['7d', '30d'], true)) {
            $cursor = $start->copy()->startOfDay();
            while ($cursor <= $now) {
                $key = $cursor->format('Y-m-d');
                $buckets[$key] = ['label' => $cursor->format('d M'), 'tokens' => 0.0, 'cost' => 0.0];
                $cursor->addDay();
            }
            $unit = 'day';
        } elseif (in_array($this->range, ['live', '1h'], true)) {
            $step = 5;
            $cursor = $start->copy()->second(0);
            $cursor->minute((int) (floor($cursor->minute / $step) * $step));
            while ($cursor <= $now) {
                $key = $cursor->format('Y-m-d H:i');
                $buckets[$key] = ['label' => $cursor->format('H:i'), 'tokens' => 0.0, 'cost' => 0.0];
                $cursor->addMinutes($step);
            }
            $unit = 'minute';
        } else {
            $cursor = $start->copy()->minute(0)->second(0);
            while ($cursor <= $now) {
                $key = $cursor->format('Y-m-d H:00');
                $buckets[$key] = ['label' => $cursor->format('H:i'), 'tokens' => 0.0, 'cost' => 0.0];
                $cursor->addHour();
            }
            $unit = 'hour';
        }

        $rows = (clone $usage)->select('created_at', 'total_tokens', 'cost')->get();
        foreach ($rows as $row) {
            $at = Carbon::parse($row->created_at);
            if ($unit === 'day') {
                $key = $at->format('Y-m-d');
            } elseif ($unit === 'minute') {
                $at = $at->copy()->second(0);
                $at->minute((int) (floor($at->minute / 5) * 5));
                $key = $at->format('Y-m-d H:i');
            } else {
                $key = $at->format('Y-m-d H:00');
            }
            if (! isset($buckets[$key])) {
                continue;
            }
            $buckets[$key]['tokens'] += (int) $row->total_tokens;
            $buckets[$key]['cost'] += (float) $row->cost;
        }

        return array_values($buckets);
    }

    public function renderAiUsage()
    {
        $requests = $this->requestQuery();
        $usage = $this->usageQuery();
        $scoped = $this->scopedApplication();

        $byModel = (clone $usage)
            ->selectRaw('model, count(*) as usage_rows, coalesce(sum(input_tokens),0) as input_tokens, coalesce(sum(output_tokens),0) as output_tokens, coalesce(sum(total_tokens),0) as total_tokens, coalesce(sum(cost),0) as cost, max(created_at) as last_at')
            ->groupBy('model')
            ->orderByRaw('coalesce(sum(total_tokens),0) desc')
            ->get();

        $requestCount = (clone $requests)->count();
        $errorCount = (clone $requests)->where('status', AiRequest::STATUS_ERROR)->count();
        $successCount = (clone $requests)->where('status', AiRequest::STATUS_SUCCESS)->count();
        $avgLatency = (clone $requests)->whereNotNull('latency_ms')->avg('latency_ms');
        $inputTokens = (int) (clone $usage)->sum('input_tokens');
        $outputTokens = (int) (clone $usage)->sum('output_tokens');
        $totalTokens = (int) (clone $usage)->sum('total_tokens');
        $cost = (float) (clone $usage)->sum('cost');
        $successRate = $requestCount > 0 ? round(100 * $successCount / $requestCount, 1) : null;

        foreach ($byModel as $row) {
            $meta = $this->providerOf((string) $row->model);
            $row->provider_label = $meta['label'];
            $row->short_name = Str::afterLast((string) $row->model, '/');
            $in = (int) $row->input_tokens;
            $out = (int) $row->output_tokens;
            $tot = max(1, (int) $row->total_tokens);
            $c = (float) $row->cost;
            $row->input_cost = $c * ($in / $tot);
            $row->output_cost = $c * ($out / $tot);
            $row->last_at = $row->last_at ? Carbon::parse($row->last_at) : null;
        }

        $prev = $this->previousSummary();
        $graph = $this->buildGraph($byModel, $requests, $usage);
        $series = $this->buildSeries($usage);
        $lit = $this->litIds($graph);
        $inspected = $this->inspectedModel($graph);

        $seenQuery = AiRequest::query()->whereNotNull('model');
        $this->scopeToApplication($seenQuery);

        $byApplication = (clone $usage)
            ->selectRaw('ai_application_id, count(*) as usage_rows, coalesce(sum(input_tokens),0) as input_tokens, coalesce(sum(output_tokens),0) as output_tokens, coalesce(sum(total_tokens),0) as total_tokens, coalesce(sum(cost),0) as cost, max(created_at) as last_at')
            ->groupBy('ai_application_id')
            ->orderByRaw('coalesce(sum(total_tokens),0) desc')
            ->get();

        $appNames = AiApplication::query()
            ->whereIn('id', $byApplication->pluck('ai_application_id')->filter()->all() ?: [0])
            ->pluck('name', 'id');

        foreach ($byApplication as $row) {
            $row->application_name = $appNames[$row->ai_application_id] ?? '#'.$row->ai_application_id;
        }

        return view('livewire.ai.application-usage-page', [
            'application' => $scoped,
            'isGlobal' => $scoped === null,
            'usageSummary' => [
                'requests' => $requestCount,
                'errors' => $errorCount,
                'successes' => $successCount,
                'success_rate' => $successRate,
                'latency' => $avgLatency !== null ? (int) round($avgLatency) : null,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'cached_tokens' => 0,
                'total_tokens' => $totalTokens,
                'cost' => $cost,
                'models' => $byModel->count(),
                'trends' => [
                    'requests' => $this->trend((float) $requestCount, (float) $prev['requests']),
                    'tokens' => $this->trend((float) $totalTokens, (float) $prev['tokens']),
                    'models' => $this->trend((float) $byModel->count(), (float) $prev['models']),
                    'cost' => $this->trend($cost, (float) $prev['cost']),
                    'success' => $this->trend((float) ($successRate ?? 0), (float) ($prev['success_rate'] ?? 0)),
                ],
            ],
            'graph' => $graph,
            'series' => $series,
            'lit' => $lit,
            'inspected' => $inspected,
            'overviewModels' => $byModel->take(20)->values(),
            'overviewUsers' => ($usersAll = (clone $usage)
                ->selectRaw("coalesce(end_user_id, '(unknown)') as end_user_id, max(end_user_name) as end_user_name, count(*) as usage_rows, coalesce(sum(input_tokens),0) as input_tokens, coalesce(sum(output_tokens),0) as output_tokens, coalesce(sum(total_tokens),0) as total_tokens, coalesce(sum(cost),0) as cost, max(created_at) as last_at")
                ->groupByRaw("coalesce(end_user_id, '(unknown)')")
                ->orderByRaw('coalesce(sum(total_tokens),0) desc')
                ->get()
            )->take(20)->values(),
            'byModel' => ($modelPager = $this->slicePage($byModel, (int) $this->modelPage))['rows'],
            'modelPager' => $modelPager,
            'byApplication' => ($appPager = $this->slicePage($byApplication, (int) $this->appPage))['rows'],
            'appPager' => $appPager,
            'byUser' => ($userPager = $this->slicePage($usersAll, (int) $this->userPage))['rows'],
            'userPager' => $userPager,
            'recentRequests' => (clone $requests)
                ->with('usage')
                ->when($this->inspectModel !== '', function ($q) {
                    $q->where('model', $this->inspectModel);
                })
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
            'seenModels' => $seenQuery->distinct()->orderBy('model')->pluck('model'),
            'ranges' => [
                'live' => __('Live'),
                '1h' => '1H',
                '24h' => '24H',
                '7d' => '7D',
                '30d' => '30D',
            ],
        ]);
    }

    private function requestQuery()
    {
        return $this->applyFilters($this->scopeToApplication(AiRequest::query()));
    }

    private function usageQuery()
    {
        return $this->applyFilters($this->scopeToApplication(AiUsage::query()));
    }

    private function scopeToApplication($query)
    {
        $app = $this->scopedApplication();
        if ($app) {
            $query->where('ai_application_id', $app->id);
        }

        return $query;
    }

    private function applyFilters($query)
    {
        $query->where('created_at', '>=', $this->rangeStart());

        if ($this->usageModel !== '' && $this->tab === 'details') {
            $query->where('model', $this->usageModel);
        }
        if ($this->endUser !== '') {
            $term = $this->endUser;
            $query->where(function ($q) use ($term) {
                $q->where('end_user_id', $term)
                    ->orWhere('end_user_name', 'like', '%'.$term.'%')
                    ->orWhere('end_user_email', 'like', '%'.$term.'%');
            });
        }

        return $query;
    }

    private function rangeStart(): Carbon
    {
        if ($this->range === 'live') {
            return now()->subMinutes(15);
        }
        if ($this->range === '1h') {
            return now()->subHour();
        }
        if ($this->range === '7d') {
            return now()->subDays(7);
        }
        if ($this->range === '30d') {
            return now()->subDays(30);
        }

        return now()->subDay();
    }

    private function previousRangeStart(): Carbon
    {
        $start = $this->rangeStart();
        $span = max(60, $start->diffInSeconds(now()));

        return $start->copy()->subSeconds($span);
    }

    private function previousSummary(): array
    {
        $start = $this->rangeStart();
        $prevStart = $this->previousRangeStart();

        $requests = $this->scopeToApplication(AiRequest::query())
            ->where('created_at', '>=', $prevStart)
            ->where('created_at', '<', $start);
        $usage = $this->scopeToApplication(AiUsage::query())
            ->where('created_at', '>=', $prevStart)
            ->where('created_at', '<', $start);

        $reqCount = (clone $requests)->count();
        $ok = (clone $requests)->where('status', AiRequest::STATUS_SUCCESS)->count();

        return [
            'requests' => $reqCount,
            'tokens' => (int) (clone $usage)->sum('total_tokens'),
            'cost' => (float) (clone $usage)->sum('cost'),
            'models' => (int) (clone $usage)->selectRaw('count(distinct model) as aggregate')->value('aggregate'),
            'success_rate' => $reqCount > 0 ? round(100 * $ok / $reqCount, 1) : 0,
        ];
    }

    private function trend(float $curr, float $prev): ?array
    {
        if ($curr <= 0 && $prev <= 0) {
            return null;
        }
        if ($prev <= 0) {
            return null;
        }

        $pct = (($curr - $prev) / $prev) * 100;

        return [
            'dir' => $pct >= 0 ? 'up' : 'down',
            'label' => ($pct >= 0 ? '+' : '').number_format($pct, 1).'%',
        ];
    }
}
