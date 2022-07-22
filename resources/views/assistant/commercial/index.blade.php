<x-app-layout>
    <x-slot name="header"></x-slot>

    @include('assistant.nav')

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-sm my-2">
                <div class="mx-auto">
                    <div class="flex justify-between">
                        <div class="my-2">
                            @livewire('commercial.item.add')
                        </div>
                        @include('assistant.commercial.table-list', ['active'=>'item'])

                    </div>

                    <div class="px-4 py-2">
                        <livewire:table.commerce-item searchable="name, sku, type" exportable/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
