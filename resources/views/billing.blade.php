<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <livewire:billings-table searchable="code, description, status, amount, created_at" exportable />
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <livewire:requests-table searchable="user_id, type, created_at" exportable />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
