<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Quotation') }}</h2>
    </x-slot>

    @include('assistant.nav')

    <x-page-section>
        <x-slot name="toolbar">
            @include('assistant.commercial.table-list', ['active'=>'quotation'])
            @livewire('commercial.quotation.add')
        </x-slot>
        <livewire:table.quotation searchable="title, status, source" exportable/>
    </x-page-section>
</x-app-layout>
