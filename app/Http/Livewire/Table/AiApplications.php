<?php

namespace App\Http\Livewire\Table;

use App\Models\AiApplication;
use App\Models\AiUsage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class AiApplications extends LivewireDatatable
{
    public $model = AiApplication::class;

    protected $monthUsage;

    public function builder()
    {
        return AiApplication::query()->orderBy('name');
    }

    public function columns()
    {
        return [
            Column::name('name')->label('Name')->filterable()->searchable(),
            Column::name('slug')->label('Slug')->filterable()->searchable(),
            Column::name('api_key_prefix')->label('Key prefix'),
            Column::callback(['status'], function ($status) {
                return $status === 'active' ? 'Active' : 'Inactive';
            })->label('Status')->filterable(['active', 'inactive']),
            Column::name('rate_limit_per_minute')->label('Rate / min'),
            Column::callback(['monthly_cost_limit', 'cost_currency'], function ($limit, $currency) {
                if ($limit === null || $limit === '') {
                    return '—';
                }
                $code = strtoupper((string) $currency) === 'IDR' ? 'IDR' : 'USD';
                if ($code === 'IDR') {
                    return 'Rp '.number_format((float) $limit, 0, ',', '.');
                }

                return '$'.number_format((float) $limit, 2);
            })->label('Cost quota'),
            Column::callback(['id', 'cost_currency'], function ($id, $currency) {
                $row = $this->monthUsage()[(int) $id] ?? ['tokens' => 0, 'cost' => 0.0];
                $app = new AiApplication(['cost_currency' => $currency]);

                return e($this->formatTokens($row['tokens']).' tok · '.$app->formatCost($row['cost']));
            })->label('Usage')->unsortable(),
            Column::callback(['require_end_user'], function ($required) {
                return $required ? 'Required' : 'Optional';
            })->label('End user')->filterable(),
            Column::callback(['id', 'uuid', 'status'], function ($id, $uuid, $status) {
                return view('tables.ai-application-actions', [
                    'id' => $id,
                    'uuid' => $uuid,
                    'active' => $status === 'active',
                ]);
            })->label('Actions')->unsortable(),
        ];
    }

    protected function monthUsage(): array
    {
        if (is_array($this->monthUsage)) {
            return $this->monthUsage;
        }

        $this->monthUsage = [];
        $rows = AiUsage::query()
            ->selectRaw('ai_application_id, coalesce(sum(total_tokens),0) as tokens, coalesce(sum(cost),0) as cost')
            ->where('created_at', '>=', now()->startOfMonth())
            ->groupBy('ai_application_id')
            ->get();

        foreach ($rows as $row) {
            $this->monthUsage[(int) $row->ai_application_id] = [
                'tokens' => (int) $row->tokens,
                'cost' => (float) $row->cost,
            ];
        }

        return $this->monthUsage;
    }

    protected function formatTokens($n): string
    {
        $n = (float) $n;
        if ($n >= 1000000) {
            return rtrim(rtrim(number_format($n / 1000000, 2, '.', ''), '0'), '.').'M';
        }
        if ($n >= 1000) {
            return rtrim(rtrim(number_format($n / 1000, 1, '.', ''), '0'), '.').'K';
        }

        return number_format($n);
    }
}
