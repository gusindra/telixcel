<x-app-layout>
    <!-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot> -->

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 col-span-1">
                    <x-jet-nav-link href="#active" >
                        {{ __('Active') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="#waiting" >
                        {{ __('Waiting') }}
                    </x-jet-nav-link>
                    <!-- <x-jet-nav-link href="#close">
                        {{ __('Closed') }}
                    </x-jet-nav-link> -->
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg col-span-4 bg-blend-darken p-6 ">
                    <x-textarea
                        class="mt-1 block w-full"
                        placeholder="{{ __('write a reply...') }}"
                        wire:model="request"
                        wire:keydown.enter.prevent="sendMessage"
                    />
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 col-span-2">
                    <x-jet-input type="text" class="mt-1 block w-full mb-4" placeholder="{{ __('Customer Name') }}"
                        x-ref="name"
                        wire:model.defer="customerName"
                        wire:keydown.enter="customerName" />
                    <x-jet-input type="text" class="mt-1 block w-full" placeholder="{{ __('Customer Phone') }}"
                        x-ref="phone"
                        wire:model.defer="customerPhone"
                        wire:keydown.enter="customerPhone" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
