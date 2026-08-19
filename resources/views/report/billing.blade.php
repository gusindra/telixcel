<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Billing') }}</h2>
    </x-slot>

    @include('report.nav')

    <x-page-section :title="__('Billing Table')">
        <livewire:table.billings-table searchable="code, description, status, amount, created_at" exportable />
    </x-page-section>
</x-app-layout>
