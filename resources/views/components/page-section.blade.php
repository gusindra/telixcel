@props(['title' => null])

<section {{ $attributes->merge(['class' => 'tx-section']) }}>
    @if ($title)
        <h3 class="tx-section-title">{{ $title }}</h3>
    @endif

    <div class="tx-card">
        @isset($toolbar)
            <div class="tx-toolbar">{{ $toolbar }}</div>
        @endisset

        {{ $slot }}
    </div>
</section>
