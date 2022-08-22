<div class="p-4">
    <!-- <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" href="{{route('create.template')}}">
        {{ __('Create') }}
    </a> -->
    <div class="flex items-center justify-end">
        <x-jet-button wire:click="actionShowModal">
            {{__('Add Menu')}}
        </x-jet-button>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('New Permission') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="model" value="{{ __('Menu') }}" />
                <x-jet-input id="model" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="model" autofocus />
                <x-jet-input-error for="model" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3 flex">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="type.view" name="type.view" wire:model="type.view"
                            wire:model.defer="type.view" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="type.view" class="font-medium text-gray-700 dark:text-gray-300 ">View</label>
                    </div>
                </div>
                <div class="flex items-start ml-4">
                    <div class="flex items-center h-5">
                        <input id="type.create" name="type.create" wire:model="type.create"
                            wire:model.defer="type.create" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="type.create" class="font-medium text-gray-700 dark:text-gray-300">Create</label>
                    </div>
                </div>
                <div class="flex items-start ml-4">
                    <div class="flex items-center h-5">
                        <input id="type.update" name="type.update" wire:model="type.update"
                            wire:model.defer="type.update" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="type.update" class="font-medium text-gray-700 dark:text-gray-300">Update</label>
                    </div>
                </div>
                <div class="flex items-start ml-4">
                    <div class="flex items-center h-5">
                        <input id="type.delete" name="type.delete" wire:model="type.delete"
                            wire:model.defer="type.delete" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="type.delete" class="font-medium text-gray-700 dark:text-gray-300">Delete</label>
                    </div>
                </div>
                <div class="flex items-start ml-4">
                    <div class="flex items-center h-5">
                        <input id="type.audit" name="type.audit" wire:model="type.audit"
                            wire:model.defer="type.audit" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="type.audit" class="font-medium text-gray-700 dark:text-gray-300">Audit</label>
                    </div>
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

