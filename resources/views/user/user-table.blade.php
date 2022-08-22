<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto">
                    <div class="flex justify-between">
                        <div class="p-4">
                            @livewire('user.add')
                        </div>
                    </div>
                    <div>
                        <livewire:table.team-table searchable="name, email, gender" exportable />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
