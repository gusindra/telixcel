<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gantt Chart') }}
        </h2>
    </x-slot>

    @if(auth()->user()->currentTeam)
        @livewire('dashboard.gantt-view')
    @endif

</x-app-layout>
