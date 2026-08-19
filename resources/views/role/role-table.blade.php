<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Role') }}</h2>
    </x-slot>

    @include('settings.navigation')

    <x-page-section>
        <x-slot name="toolbar">
            @livewire('role.roles')
        </x-slot>
        <livewire:table.roles-table searchable="name" />
    </x-page-section>
</x-app-layout>
