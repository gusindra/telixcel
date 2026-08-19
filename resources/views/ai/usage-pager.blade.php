@if(($pager['total'] ?? 0) > 0)
    <div class="ai-ug-pager">
        <span>{{ __('Showing') }} {{ $pager['from'] }}–{{ $pager['to'] }} {{ __('of') }} {{ $pager['total'] }}</span>
        @if($pager['last'] > 1)
            <div class="ai-ug-pager-nav">
                <button type="button" class="tx-btn tx-btn-ghost" wire:click="{{ $method }}({{ $pager['page'] - 1 }})" @if($pager['page'] <= 1) disabled @endif>{{ __('Prev') }}</button>
                <span>{{ $pager['page'] }} / {{ $pager['last'] }}</span>
                <button type="button" class="tx-btn tx-btn-ghost" wire:click="{{ $method }}({{ $pager['page'] + 1 }})" @if($pager['page'] >= $pager['last']) disabled @endif>{{ __('Next') }}</button>
            </div>
        @endif
    </div>
@endif
