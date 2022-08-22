<x-app-layout>
    <x-slot name="header"></x-slot>


    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-sm my-2">
                <div class="container mx-auto">
                    @livewire('project.add')
                    <div class="px-4 py-2">
                        <livewire:table.project-table searchable="name" exportable/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
