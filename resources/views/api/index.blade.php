<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('API Tokens') }}
        </h2>
    </x-slot>

    <div class="tx-stack">
        <x-page-section :title="__('API Tokens')">
            @livewire('api.api-token-manager')
        </x-page-section>
        <x-page-section :title="__('Webhooks')">
            @livewire('api.webhook-manager')
        </x-page-section>
    </div>
</x-app-layout>
