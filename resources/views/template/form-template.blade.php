<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Template') }}
        </h2>
    </x-slot>

    <x-page-section>
        @livewire('form-templates')
    </x-page-section>
</x-app-layout>
