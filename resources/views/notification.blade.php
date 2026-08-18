<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Notification') }}</h2>
    </x-slot>

    @include('settings.navigation', ['page' => 'notification'])

    <x-page-section>
        <x-slot name="toolbar">
            <a href="{{ route('notification.read.all') }}" class="tx-btn tx-btn-ghost">{{ __('Read All') }}</a>
            @livewire('setting.notification.add')
        </x-slot>
        <livewire:table.notification-table searchable="id" exportable />
    </x-page-section>
</x-app-layout>
