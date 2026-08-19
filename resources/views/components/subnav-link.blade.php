@props(['active' => false])

@php
$classes = ($active ?? false) ? 'tx-tab tx-tab-active' : 'tx-tab';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
