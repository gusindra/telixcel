<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('AI') }}
        </h2>
    </x-slot>

    <div class="py-2">
        @livewire('agent-console')
    </div>
</x-app-layout>
