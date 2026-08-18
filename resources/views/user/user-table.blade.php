<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Users') }}</h2>
    </x-slot>

    <x-page-section>
        <x-slot name="toolbar">
            @livewire('user.add')
        </x-slot>
        <livewire:table.team-table searchable="name, email, gender" exportable />
    </x-page-section>
</x-app-layout>
