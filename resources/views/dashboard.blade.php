<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- First User Member to create Team -->
    @if (Auth::user()->currentTeam && Laravel\Jetstream\Jetstream::hasTeamFeatures())
    <!-- {{Auth::user()->currentTeam}} -->
    @endif

    @if(auth()->user()->currentTeam)
        <!-- Dashboard Livewire Component -->
        @livewire('dashboard.dashboard-overview')
    @endif

</x-app-layout>
