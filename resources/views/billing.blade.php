<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Billing') }}</h2>
    </x-slot>

    @include('report.nav')

    <div class="tx-stack">
        <x-page-section :title="__('Billing Table')">
            <livewire:table.billings-table searchable="code, description, status, amount, created_at" exportable />
        </x-page-section>

        <x-page-section :title="__('Request Table')">
            <livewire:table.requests-table searchable="user_id, type, created_at" exportable />
        </x-page-section>
    </div>
</x-app-layout>
