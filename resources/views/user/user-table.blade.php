<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto">
                    <div class="flex justify-between">
                        <div class="p-4">
                            @livewire('user.add')
                        </div>
                    </div>
                    <div>
                        <livewire:team-table searchable="name, email, gender" exportable />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
