<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Dashboard') }}</h2>
    </x-slot>

    @if (auth()->user()->currentTeam)
        @livewire('dashboard.dashboard-overview')
    @endif
</x-app-layout>
