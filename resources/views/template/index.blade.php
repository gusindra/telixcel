<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Template') }}</h2>
    </x-slot>

    <x-page-section>
        <x-slot name="toolbar">
            @if (auth()->user()->currentTeam->id != 1)
                <div class="flex flex-wrap items-center gap-2">
                    @livewire('template.templates')
                    <a class="tx-btn" href="{{ route('view.template') }}">{{ __('View Tree') }}</a>
                </div>
            @endif
        </x-slot>
        <livewire:table.templates-table searchable="name, description" />
    </x-page-section>
</x-app-layout>
