<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>
    <div>
        <div class="max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 mb-6">
            @livewire('user.profile', ['user' => $user])
        </div>
    </div>
</x-app-layout>
