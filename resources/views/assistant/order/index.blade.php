<x-app-layout>
    <x-slot name="header"></x-slot>

    @if(request()->routeIs('commercial'))
        @include('assistant.nav')
    @endif

    @include('assistant.order.nav')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="mx-auto">
                    <div class="flex justify-between">
                        <div class="p-4">
                            @livewire('order.add')
                        </div>
                    </div>
                    <div class="px-4 py-2">
                        <livewire:table.order searchable="name" exportable/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
