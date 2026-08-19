<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Log API Request') }}</h2>
    </x-slot>

    @include('report.nav')

    <x-page-section :title="__('API Log')">
        @livewire('report.log')
    </x-page-section>
</x-app-layout>
