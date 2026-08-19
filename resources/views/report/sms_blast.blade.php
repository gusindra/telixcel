<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Log SMS') }}</h2>
    </x-slot>

    @include('report.nav')

    <x-page-section :title="__('SMS Blast Table')">
        <livewire:table.sms-blast-table searchable="{{auth()->user()->super->first()->role == 'superadmin' ? 'user_id, status, created_at':'status, msisdn, created_at, message_content'" exportable />
    </x-page-section>
</x-app-layout>
