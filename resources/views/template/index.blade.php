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
                <div class="flex">
                    @livewire('template.templates')
                    <a class="inline-flex items-center bg-gray-800 border border-transparent rounded-md h-8 p-4 mt-4 font-semibold text-xs text-white uppercase hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" href="{{route('view.template')}}">View Tree</a>
                </div>
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
