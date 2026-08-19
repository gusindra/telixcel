@component('ai.layout')
    <x-page-section>
        <x-slot name="toolbar">
            @livewire('ai.applications-page')
        </x-slot>
        <livewire:table.ai-applications searchable="name,slug" />
    </x-page-section>
@endcomponent
