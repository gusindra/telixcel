@props(['active' => false, 'label' => null])

@php
$classes = ($active ?? false) ? 'tx-nav tx-nav-active' : 'tx-nav';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'data-label' => $label]) }} @click="closeMobileNav()">
    {{ $slot }}
</a>
