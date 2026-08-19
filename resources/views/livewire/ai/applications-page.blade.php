<div>
    <x-jet-button type="button" wire:click="actionShowModal">{{ __('Add Application') }}</x-jet-button>

    <x-jet-dialog-modal wire:model="modalVisible">
        <x-slot name="title">{{ __('New Application') }}</x-slot>
        <x-slot name="content">
            <p class="text-sm text-gray-500 dark:text-gray-300 mb-4">{{ __('Only a name is needed. Rate limits, models, and the API key are set on the detail page.') }}</p>
            <div>
                <x-jet-label value="Name" />
                <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="input.name" autofocus />
                <x-jet-input-error for="input.name" class="mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('modalVisible', false)">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="save">{{ __('Create') }}</x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
