<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription') }}
        </h2>
    </x-slot>

    @include('report.nav')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-2">
            <div class="sm:px-6 lg:px-6">
                <h3 class="font-semibold text-gray-800 leading-tight pb-2">
                    {{ __('Billing Table') }}
                </h3>
                <div class="overflow-hidden shadow-xl sm:rounded-lg">
                    @livewire('report.log')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
