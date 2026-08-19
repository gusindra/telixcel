<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Invoice') }}</h2>
    </x-slot>

    @include('assistant.order.nav')

    <x-page-section>
        <x-slot name="toolbar">
            @livewire('invoice.incoming-add')
        </x-slot>
        <livewire:all-billing-table searchable="code, description" exportable />
    </x-page-section>
</x-app-layout>
