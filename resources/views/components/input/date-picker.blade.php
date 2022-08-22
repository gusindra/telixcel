@props([
    'error' => null
])

<div
    x-data="{ value: @entangle($attributes->wire('model')),show:'{{ $attributes['show']}}' }"
    x-on:change="value = $event.target.value"
    x-init="new Pikaday({ field: $refs.input, 'format': 'd M Y' });"
>
    <input
        {{ $attributes->whereDoesntStartWith('wire:model') }}
        {{ $attributes['show']?"disabled":"" }}
        x-ref="input"
        x-bind:value="value"
        type="text"
        class="pl-1 block w-full text-base shadow-sm mt-1 bg-gray-50 dark:bg-slate-800 border-gray-300 @if($error) focus:ring-danger-500 focus:border-danger-500 border-danger-500 text-danger-500 pr-10 @else focus:ring-primary-500 focus:border-primary-500 @endif rounded-md"
    />
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>

