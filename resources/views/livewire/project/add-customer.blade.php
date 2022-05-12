<div>
    <x-jet-section-border />

    <x-jet-form-section submit="save">
        <x-slot name="title">
            {{ __('Customer Details') }} (Party A)
        </x-slot>

        <x-slot name="description">
            {{ __('Information customer or entity of party A.') }}
        </x-slot>

        <x-slot name="form">

            <div class="col-span-6 sm:col-span-6">
                <div class="flex1 items-start my-2" wire:poll>
                    <div class="col-span-6 grid grid-cols-2">
                        <div class="col-span-12 sm:col-span-1">
                            <x-jet-label for="name" value="{{ __('Customer Name') }}" />
                            <x-jet-input id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        wire:model="customer_name"
                                        wire:model.defer="customer_name"
                                        wire:model.debunce.800ms="customer_name" />
                            <x-jet-input-error for="name" class="mt-2" />
                            @error('name') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </div>

                <div class="flex1 items-start my-2" wire:poll>
                    <div class="col-span-12 grid grid-cols-1">
                        <x-jet-label for="name" value="{{ __('Address') }}" />
                        <textarea  wire:model="customer_address"
                                        wire:model.defer="customer_address"
                                        wire:model.debunce.800ms="customer_address"  class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" x-bind:autofocus="isSet"></textarea>
                        <x-jet-input-error for="name" class="mt-2" />
                    </div>

                </div>
            </div>

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Customer Details (Party A) saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>
</div>
