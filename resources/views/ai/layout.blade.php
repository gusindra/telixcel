<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('AI Gateway') }}</h2>
    </x-slot>

    @include('ai.nav')

    {{ $slot }}
</x-app-layout>
