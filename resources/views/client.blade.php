<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto">
                    <div>
                        <livewire:livewire-datatables searchable="name, email, gender" exportable />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
