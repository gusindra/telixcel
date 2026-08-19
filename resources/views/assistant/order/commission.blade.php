<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Commissions') }}</h2>
    </x-slot>

    @include('assistant.order.nav')

    <x-page-section>
        <livewire:table.commission exportable/>
    </x-page-section>
</x-app-layout>
