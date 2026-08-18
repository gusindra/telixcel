<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Customer') }}</h2>
    </x-slot>

    <x-page-section>
        <livewire:table.client-datatables searchable="name, email, gender" exportable />
    </x-page-section>
</x-app-layout>
