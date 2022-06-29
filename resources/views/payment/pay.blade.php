<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Detail') }}
        </h2>
    </x-slot>
    <!-- Payment Dashboard -->
    @livewire('payment.transfer', ['order'=>$order])

</x-app-layout>
