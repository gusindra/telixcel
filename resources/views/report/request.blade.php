<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Log Chat') }}</h2>
    </x-slot>

    @include('report.nav')

    <x-page-section :title="__('Request Table')">
        <livewire:table.requests-table searchable="user_id, type, created_at" exportable />
    </x-page-section>
</x-app-layout>
