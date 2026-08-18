<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Calendar') }}
        </h2>
    </x-slot> 

    @if(auth()->user()->currentTeam)
        <!-- Dashboard Livewire Component -->
        <x-page-section>
            @livewire('dashboard.calendar-view')
        </x-page-section>
    @endif

</x-app-layout>
