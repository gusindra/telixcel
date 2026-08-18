<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Menu') }}</h2>
    </x-slot>

    @include('settings.navigation', ['page'=>$page])

    <x-page-section>
        <x-slot name="toolbar">
            @livewire('permission.add')
        </x-slot>
        <livewire:table.permission searchable="name" />
    </x-page-section>
</x-app-layout>
