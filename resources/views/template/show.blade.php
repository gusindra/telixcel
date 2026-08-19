<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Template') }}
        </h2>
    </x-slot>

    <x-page-section>
        @livewire('template.edit-template', ['uuid'=>$uuid])
    </x-page-section>
</x-app-layout>
