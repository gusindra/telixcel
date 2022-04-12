<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription') }}
        </h2>
    </x-slot>

    @include('report.nav')

    <div class="py-4">
        <div class="p-6 sm:px-20 bg-opacity-25 grid grid-cols-1">
            <div class="sm:px-6 lg:px-6">
                <h3 class="font-semibold text-gray-800 leading-tight pb-2">
                    {{ __('Billing Table') }}
                </h3>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <livewire:billings-table searchable="code, description, status, amount, created_at" exportable />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
