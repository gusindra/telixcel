@php
    $kind = $kind ?? 'model';
    $rows = $rows ?? collect();
@endphp

<div class="ai-break" x-data="{ on: true, open: '' }">
    <div class="ai-break-bar">
        <button type="button" class="ai-break-toggle" @click="on = !on">
            <span class="material-symbols-outlined ai-break-caret" :class="on && 'is-open'">chevron_right</span>
            <span class="ai-break-title">{{ $title }}</span>
        </button>
        <div class="ai-usage-seg" role="tablist">
            <button type="button" class="{{ $tableMetric === 'cost' ? 'is-on' : '' }}" wire:click="setTableMetric('cost')">{{ __('Costs') }}</button>
            <button type="button" class="{{ $tableMetric === 'tokens' ? 'is-on' : '' }}" wire:click="setTableMetric('tokens')">{{ __('Tokens') }}</button>
        </div>
    </div>

    <div class="overflow-x-auto" x-show="on">
        <table class="min-w-full text-sm ai-break-table">
            <thead class="text-left tx-fg-muted">
                <tr>
                    <th class="px-3 py-2" style="width:28px;"></th>
                    <th class="px-3 py-2">{{ $kind === 'model' ? __('Model') : __('Customer') }}</th>
                    <th class="px-3 py-2">{{ $kind === 'model' ? __('Provider') : __('ID') }}</th>
                    <th class="px-3 py-2">{{ __('Requests') }}</th>
                    <th class="px-3 py-2">{{ __('Last used') }}</th>
                    @if($tableMetric === 'cost')
                        <th class="px-3 py-2">{{ __('Input cost') }}</th>
                        <th class="px-3 py-2">{{ __('Output cost') }}</th>
                        <th class="px-3 py-2">{{ __('Total cost') }}</th>
                    @else
                        <th class="px-3 py-2">{{ __('Input') }}</th>
                        <th class="px-3 py-2">{{ __('Output') }}</th>
                        <th class="px-3 py-2">{{ __('Tokens') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php
                        $key = $kind === 'model' ? (string) $row->model : (string) $row->end_user_id;
                        $in = (int) $row->input_tokens;
                        $out = (int) $row->output_tokens;
                        $tot = max(1, (int) $row->total_tokens);
                        $cost = (float) $row->cost;
                        $inCost = $cost * ($in / $tot);
                        $outCost = $cost * ($out / $tot);
                        $last = $row->last_at ? \Carbon\Carbon::parse($row->last_at) : null;
                    @endphp
                    <tr class="ai-break-row" @click="open = open === @js($key) ? '' : @js($key)">
                        <td class="px-3 py-2">
                            <span class="material-symbols-outlined ai-break-caret" :class="open === @js($key) && 'is-open'">chevron_right</span>
                        </td>
                        <td class="px-3 py-2">
                            @if($kind === 'model')
                                <div class="font-medium tx-fg">{{ $row->short_name }}</div>
                                <div class="font-mono text-xs tx-fg-muted">{{ $row->model }}</div>
                            @else
                                <div class="font-medium tx-fg">{{ $row->end_user_name ?: '—' }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ $kind === 'model' ? ($row->provider_label ?: '—') : $row->end_user_id }}</td>
                        <td class="px-3 py-2">{{ number_format($row->usage_rows) }}</td>
                        <td class="px-3 py-2">{{ $last ? $last->diffForHumans() : '—' }}</td>
                        @if($tableMetric === 'cost')
                            <td class="px-3 py-2">{{ $this->formatMoney($inCost) }}</td>
                            <td class="px-3 py-2">{{ $this->formatMoney($outCost) }}</td>
                            <td class="px-3 py-2 font-semibold">{{ $this->formatMoney($cost) }}</td>
                        @else
                            <td class="px-3 py-2">{{ number_format($in) }}</td>
                            <td class="px-3 py-2">{{ number_format($out) }}</td>
                            <td class="px-3 py-2 font-semibold">{{ number_format($row->total_tokens) }}</td>
                        @endif
                    </tr>
                    <tr class="ai-break-detail" x-show="open === @js($key)" x-cloak>
                        <td colspan="8" class="px-3 py-3">
                            <dl class="ai-break-meta">
                                <div>
                                    <dt>{{ __('Requests') }}</dt>
                                    <dd>{{ number_format($row->usage_rows) }}</dd>
                                </div>
                                <div>
                                    <dt>{{ __('Input tokens') }}</dt>
                                    <dd>{{ number_format($in) }}</dd>
                                </div>
                                <div>
                                    <dt>{{ __('Output tokens') }}</dt>
                                    <dd>{{ number_format($out) }}</dd>
                                </div>
                                <div>
                                    <dt>{{ __('Total tokens') }}</dt>
                                    <dd>{{ number_format($row->total_tokens) }}</dd>
                                </div>
                                <div>
                                    <dt>{{ __('Input cost') }}</dt>
                                    <dd>{{ $this->formatMoney($inCost) }}</dd>
                                </div>
                                <div>
                                    <dt>{{ __('Output cost') }}</dt>
                                    <dd>{{ $this->formatMoney($outCost) }}</dd>
                                </div>
                                <div>
                                    <dt>{{ __('Total cost') }}</dt>
                                    <dd>{{ $this->formatMoney($cost) }}</dd>
                                </div>
                                <div>
                                    <dt>{{ __('Last used') }}</dt>
                                    <dd>{{ $last ? $last->toDateTimeString() : '—' }}</dd>
                                </div>
                            </dl>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-6 text-center tx-fg-muted">{{ $empty }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
