<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Topup Detail') }}
        </h2>
    </x-slot>
    <!-- Topup Dashboard -->
    <div class="tx-stack">
        <x-page-section :title="__('Top up')">
            @livewire('saldo.topup-user')
        </x-page-section>
        <x-page-section>
            @livewire('payment.history')
        </x-page-section>
    </div>
</x-app-layout>
