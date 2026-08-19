<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Ticket') }}</h2>
    </x-slot>

    <x-page-section>
        @livewire('ticket.board')
    </x-page-section>
</x-app-layout>
