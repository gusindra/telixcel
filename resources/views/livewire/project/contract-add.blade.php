<div>
    <div class="flex items-center text-right">
        <a wire:click="actionShowModal" class="cursor-pointer inline-flex items-center px-2 py-1 bg-green-800 border border-transparent rounded-sm font-normal text-xs text-white 1g-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition">
            {{ __('+ Contract') }}
        </a>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('New Contract') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="title" value="{{ __('Title') }}" />
                <x-jet-input id="title" type="text" class="mt-1 block w-full" wire:model.defer="title" autofocus />
                <x-jet-input-error for="title" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="signer_email" value="{{ __('Signer Email (optional)') }}" />
                <x-jet-input id="signer_email" type="email" class="mt-1 block w-full" wire:model.defer="signer_email" />
                <x-jet-input-error for="signer_email" class="mt-2" />
            </div>
            <p class="px-3 text-xs text-gray-400">{{ __('This contract belongs to the project and is not tied to a client.') }}</p>
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
