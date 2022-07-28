<div>
    <div class="flex items-center text-right">
        <a wire:click="actionShowModal" class="inline-flex items-center px-2 py-1 bg-green-800 border border-transparent rounded-sm font-normal text-xs text-white 1g-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="actionShowModal">
            {{__('+ User')}}
        </a>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('New User') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="input.name" value="{{ __('Name') }}" />
                <x-jet-input id="input.name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.name" autofocus />
                <x-jet-input-error for="input.name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="input.email" value="{{ __('Email') }}" />
                <x-jet-input autocomplete="off" id="input.email" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.email" autofocus />
                <x-jet-input-error for="input.email" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3 grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <x-jet-label for="input.password" value="{{ __('Password') }}" />
                    <!-- <x-jet-input id="input.password" type="password" class="mt-1 block w-full" wire:model.debunce.800ms="input.password" autofocus /> -->
                    <div class="relative" x-data="{ input: 'password' }">
                        <input autocomplete="off" id="input.password" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full" wire:model.debunce.800ms="input.password" autofocus name="input.password" type="password" x-bind:type="input">
                        <div class="absolute right-0 top-0 mr-2 mt-2" x-on:click="input = (input === 'password') ? 'text' : 'password'">
                            <span class="body text-xs text-show-hide text-gray-600 uppercase cursor-pointer" x-text="input == 'password' ? 'show' : 'hide'">show</span>
                        </div>
                    </div>
                    <x-jet-input-error for="input.password" class="mt-2" />
                </div>
                <div class="col-span-1">
                    <x-jet-secondary-button class="mt-6 py-3" wire:click="generatePassword" wire:loading.attr="disabled">
                        {{ __('Auto Password') }}
                    </x-jet-secondary-button>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalActionVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>

