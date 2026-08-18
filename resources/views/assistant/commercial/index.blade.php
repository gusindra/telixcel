<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Product Master Data') }}</h2>
    </x-slot>

    @include('assistant.nav')

    <x-page-section>
        <x-slot name="toolbar">
            <div>
                @livewire('commercial.item.add')
            </div>
            @include('assistant.commercial.table-list', ['active'=>'item'])
        </x-slot>
        <livewire:table.commerce-item searchable="name, sku, type" exportable/>
    </x-page-section>
</x-app-layout>
