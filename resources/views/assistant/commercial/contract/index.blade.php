<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Contract') }}</h2>
    </x-slot>

    @include('assistant.nav')

    <x-page-section>
        <x-slot name="toolbar">
            @include('assistant.commercial.table-list', ['active'=>'contract'])
            @livewire('commercial.contract.add')
        </x-slot>
        <livewire:table.contract searchable="title, source" exportable/>
    </x-page-section>
</x-app-layout>
