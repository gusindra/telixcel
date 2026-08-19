<div class="ai-ug-kpis" wire:key="usage-kpis">
    <div class="ai-ug-kpi">
        <span class="ai-ug-kpi-label">{{ __('Requests') }}</span>
        <strong class="ai-ug-kpi-value">{{ number_format($usageSummary['requests']) }}</strong>
        @if($usageSummary['trends']['requests'])
            <span class="ai-ug-trend is-{{ $usageSummary['trends']['requests']['dir'] }}">{{ $usageSummary['trends']['requests']['label'] }}</span>
        @endif
    </div>
    <div class="ai-ug-kpi">
        <span class="ai-ug-kpi-label">{{ __('Tokens') }}</span>
        <strong class="ai-ug-kpi-value" title="{{ number_format($usageSummary['total_tokens']) }}">{{ $fmt($usageSummary['total_tokens']) }}</strong>
        @if($usageSummary['trends']['tokens'])
            <span class="ai-ug-trend is-{{ $usageSummary['trends']['tokens']['dir'] }}">{{ $usageSummary['trends']['tokens']['label'] }}</span>
        @endif
    </div>
    <div class="ai-ug-kpi">
        <span class="ai-ug-kpi-label">{{ __('Models') }}</span>
        <strong class="ai-ug-kpi-value">{{ number_format($usageSummary['models']) }}</strong>
        @if($usageSummary['trends']['models'])
            <span class="ai-ug-trend is-{{ $usageSummary['trends']['models']['dir'] }}">{{ $usageSummary['trends']['models']['label'] }}</span>
        @endif
    </div>
    <div class="ai-ug-kpi">
        <span class="ai-ug-kpi-label">{{ __('Estimated cost') }}</span>
        <strong class="ai-ug-kpi-value">{{ $this->formatMoney($usageSummary['cost']) }}</strong>
        @if($usageSummary['trends']['cost'])
            <span class="ai-ug-trend is-{{ $usageSummary['trends']['cost']['dir'] }}">{{ $usageSummary['trends']['cost']['label'] }}</span>
        @endif
    </div>
    <div class="ai-ug-kpi">
        <span class="ai-ug-kpi-label">{{ __('Success') }}</span>
        <strong class="ai-ug-kpi-value">{{ $usageSummary['success_rate'] === null ? '—' : $usageSummary['success_rate'].'%' }}</strong>
        @if($usageSummary['success_rate'] !== null && $usageSummary['trends']['success'])
            <span class="ai-ug-trend is-{{ $usageSummary['trends']['success']['dir'] }}">{{ $usageSummary['trends']['success']['label'] }}</span>
        @endif
    </div>
</div>
