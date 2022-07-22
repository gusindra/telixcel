<x-app-layout>
    <x-slot name="header"></x-slot>

    @include('assistant.nav')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto">
                    <div class="flex justify-between">
                        <div class="p-4">
                            @livewire('commercial.quotation.add')
                        </div>
                        @include('assistant.commercial.table-list', ['active'=>'quotation'])
                    </div>

                    <div class="px-4 py-1">
                        <livewire:table.quotation searchable="title, status, source" exportable/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
