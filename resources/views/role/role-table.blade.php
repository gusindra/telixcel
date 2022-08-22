<x-app-layout>
    <x-slot name="header"></x-slot>

    @include('settings.navigation')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">

                <div class="container mx-auto">
                    @livewire('role.roles')
                    <!-- <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6 right-5 mr-0">
                        <x-jet-button>
                            {{__('Add New Role')}}
                        </x-jet-button>
                    </div> -->
                    <div class="px-4 py-2">
                        <livewire:table.roles-table searchable="name" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
