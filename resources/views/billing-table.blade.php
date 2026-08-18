<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Bill') }}</h2>
    </x-slot>

    <x-page-section>
        <x-slot name="toolbar">
            <a class="tx-btn" href="{{ url('/template/create') }}">{{ __('Generate') }}</a>
        </x-slot>
        <livewire:table.all-billing-table searchable="name, email, gender" exportable />
    </x-page-section>
</x-app-layout>
