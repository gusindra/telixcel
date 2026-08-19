@if($inspected)
    <div class="ai-ug-modal"
         wire:key="inspect-{{ $inspected['id'] }}"
         x-data
         @keydown.escape.window="@this.call('clearInspect')">
        <button type="button" class="ai-ug-modal-scrim" wire:click="clearInspect" aria-label="{{ __('Close') }}"></button>
        <aside class="ai-ug-modal-panel" role="dialog" aria-modal="true" aria-labelledby="ai-ug-inspect-title">
            <div class="ai-ug-inspect-top">
                <div>
                    <p class="ai-ug-inspect-kicker">{{ $inspected['provider_label'] }} · {{ __('Model') }}</p>
                    <h3 id="ai-ug-inspect-title">{{ $inspected['name'] }}</h3>
                    <p class="ai-ug-inspect-full">{{ $inspected['full'] }}</p>
                </div>
                <button type="button" class="ai-ug-iconbtn" wire:click="clearInspect" title="{{ __('Close') }}">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <dl class="ai-ug-inspect-grid">
                <div><dt>{{ __('Requests') }}</dt><dd>{{ number_format($inspected['requests']) }}</dd></div>
                <div><dt>{{ __('Input tokens') }}</dt><dd>{{ number_format($inspected['input_tokens']) }}</dd></div>
                <div><dt>{{ __('Output tokens') }}</dt><dd>{{ number_format($inspected['output_tokens']) }}</dd></div>
                <div><dt>{{ __('Total tokens') }}</dt><dd>{{ number_format($inspected['tokens']) }}</dd></div>
                <div><dt>{{ __('Estimated cost') }}</dt><dd>{{ $this->formatMoney($inspected['cost']) }}</dd></div>
                <div><dt>{{ __('Average latency') }}</dt><dd>{{ $inspected['latency'] === null ? '—' : $inspected['latency'].' ms' }}</dd></div>
                <div><dt>{{ __('Success rate') }}</dt><dd>{{ $inspected['success'] === null ? '—' : $inspected['success'].'%' }}</dd></div>
                <div><dt>{{ __('Last activity') }}</dt><dd>{{ $inspected['last_at'] ? $inspected['last_at']->diffForHumans() : '—' }}</dd></div>
            </dl>
            <h4>{{ __('Recent on this path') }} <span class="ai-ug-inspect-cap">{{ __('Latest 5') }}</span></h4>
            <div class="ai-ug-inspect-list">
                @forelse($recentRequests as $item)
                    <div class="ai-ug-inspect-row">
                        <span class="ai-ug-inspect-id">{{ \Illuminate\Support\Str::limit($item->request_id, 18, '') }}</span>
                        <span class="ai-ug-state is-{{ $item->status }}">{{ $item->status }}</span>
                        <span>{{ $item->usage ? number_format((int) $item->usage->total_tokens) : '—' }}</span>
                    </div>
                @empty
                    <p class="ai-ug-empty">{{ __('No requests yet.') }}</p>
                @endforelse
            </div>
        </aside>
    </div>
@endif
