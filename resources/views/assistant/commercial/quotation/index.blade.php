<x-app-layout>
    <x-slot name="header"></x-slot>

    @include('assistant.nav')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto">
                <div class="justify-end flex p-4">
                    <div class="flex items-center justify-end px-2 text-right">
                        <a href="{{ route('commercial.show', ['item']) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="actionShowModal">
                            {{__('Product Master Data')}}
                        </a>
                    </div>
                    <div class="flex items-center justify-end px-2 text-right">
                        <a href="{{ route('commercial.show', ['quotation']) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="actionShowModal">
                            {{__('Quotation')}}
                        </a>
                    </div>
                    <div class="flex items-center justify-end px-2 text-right">
                        <a href="{{ route('commercial.show', ['contract']) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="actionShowModal">
                            {{__('Contract')}}
                        </a>
                    </div>
                </div>


                    <div class="px-4 py-2">
                        <livewire:table.project-table searchable="name" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
