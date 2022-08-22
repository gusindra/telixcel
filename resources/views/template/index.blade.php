<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Template') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-600  overflow-hidden shadow-xl sm:rounded-lg">
                @if (auth()->user()->currentTeam->id != 1 )
                    @livewire('template.templates')
                @endif

                <div class="p-3">
                    <div>
                        <livewire:table.templates-table searchable="name, description" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
