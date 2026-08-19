@php
    $items = array_values($items ?? []);
    $count = count($items);
    $current = $count ? $items[$count - 1]['label'] : __('Dashboard');
    $visible = $count > 1 ? array_slice($items, 1) : [];
    $visibleCount = count($visible);
@endphp

<nav class="tx-crumb" aria-label="{{ __('Breadcrumb') }}">
    <ol class="tx-crumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="tx-crumb-home" title="{{ __('Dashboard') }}">
                <span class="material-symbols-outlined" aria-hidden="true">home</span>
                <span class="tx-crumb-sr">{{ __('Dashboard') }}</span>
            </a>
        </li>

        @if($count === 1)
            <li class="tx-crumb-step is-current">
                <span class="tx-crumb-text" aria-current="page">{{ $current }}</span>
            </li>
        @endif

        @foreach($visible as $i => $item)
            @php
                $isLast = $i === $visibleCount - 1;
                $isMid = $visibleCount > 2 && $i < $visibleCount - 2;
            @endphp
            @if($i === 0 && $visibleCount > 2)
                <li class="tx-crumb-ellipsis" aria-hidden="true">
                    <span class="tx-crumb-sep">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </span>
                    <span class="tx-crumb-dots">...</span>
                </li>
            @endif
            <li class="tx-crumb-step {{ $isLast ? 'is-current' : '' }} {{ $isMid ? 'is-mid' : '' }}">
                <span class="tx-crumb-sep" aria-hidden="true">
                    <span class="material-symbols-outlined">chevron_right</span>
                </span>
                @if(! $isLast && ! empty($item['url']))
                    <a href="{{ $item['url'] }}" class="tx-crumb-link">{{ $item['label'] }}</a>
                @else
                    <span class="tx-crumb-text" @if($isLast) aria-current="page" @endif>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
