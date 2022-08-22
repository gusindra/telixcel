<x-app-layout>
    <x-slot name="header"></x-slot>

    @include('settings.navigation', ['page'=>$page])

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto">
                    @livewire('permission.add')
                    <div class="px-4 py-2">
                        <livewire:table.permission searchable="name" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
