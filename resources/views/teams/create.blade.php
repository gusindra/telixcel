<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Team') }}
        </h2>
    </x-slot>

    <x-page-section>
        @livewire('teams.create-team-form')
    </x-page-section>
</x-app-layout>
