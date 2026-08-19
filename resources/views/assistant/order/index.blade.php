<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Order') }}</h2>
    </x-slot>

    @include('assistant.order.nav')

    <x-page-section>
        <x-slot name="toolbar">
            @livewire('order.add')
        </x-slot>
        <livewire:table.order searchable="name" exportable/>
    </x-page-section>
</x-app-layout>
