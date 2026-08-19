<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Company') }}</h2>
    </x-slot>

    @include('settings.navigation', ['page'=>$page])

    <x-page-section>
        <x-slot name="toolbar">
            @livewire('setting.company.company-add')
        </x-slot>
        <livewire:table.companies searchable="name" />
    </x-page-section>
</x-app-layout>
