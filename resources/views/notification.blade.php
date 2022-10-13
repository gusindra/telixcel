<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notification') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto">
                    <a href="{{route('notification.read.all')}}" class="p-2 text-xs">Read All Notification</a>
                    @livewire('setting.notification.add')

                    <div>
                        <livewire:table.notification-table searchable="id" exportable />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
