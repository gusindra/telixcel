<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>
    <x-page-section>
        @livewire('user.profile', ['user' => $user])
    </x-page-section>
</x-app-layout>
