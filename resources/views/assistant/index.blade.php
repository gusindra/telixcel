<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Project') }}</h2>
    </x-slot>

    <x-page-section>
        <x-slot name="toolbar">
            @livewire('project.add')
        </x-slot>
        <livewire:table.project-table searchable="name" />
    </x-page-section>
</x-app-layout>
