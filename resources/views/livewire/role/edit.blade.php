<div>
    <x-jet-form-section submit="update({{$role->id}})">
        <x-slot name="title">
            {{ __('Role') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Role`s information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 grid grid-cols-2">
                <!-- Role Name -->
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Name') }}" />
                    <x-jet-input id="name"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="name"
                                wire:model.defer="name"
                                wire:model.debunce.800ms="name" />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
            </div>

            <!-- Template Description -->
            <div class="col-span-6 sm:col-span-6">
                <x-jet-label for="description" value="{{ __('Description') }}" />

                <x-textarea wire:model="description"
                            wire:model.defer="description"
                            value="description" class="mt-1 block w-full" :disabled="! Gate::check('update', $role)"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
            </div>

            <div class="col-span-6 grid grid-cols-2">
                <!-- Type Information -->
                <div class="col-span-12 sm:col-span-1">
                    <div>
                        <x-jet-label for="type" value="{{ __('Type') }}" />
                    </div>
                    <select
                        name="type"
                        id="type"
                        class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="type"
                        >
                        <option value="admin" selected>Admin</option>
                        <option value="team">Team</option>
                        <option value="master">Master</option>
                    </select>

                    <x-jet-input-error for="type" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 grid grid-cols-2">
                <!-- Type Information -->
                <div class="col-span-12 sm:col-span-1">
                    <div>
                        <x-jet-label for="role_for" value="{{ __('Role for') }}" />
                    </div>
                    <select
                        name="role_for"
                        id="role_for"
                        class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="role_for"
                        >
                        <option value="admin" selected>Admin</option>
                        <option value="team">Team</option>
                    </select>

                    <x-jet-input-error for="role_for" class="mt-2" />
                </div>
            </div>

            <!-- is_enabled -->
            <div class="col-span-6 sm:col-span-6">

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="status" name="status" wire:model="status"
                            wire:model.defer="status" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="status" class="font-medium text-gray-700 dark:text-slate-300">is enable ?</label>
                        <p class="text-gray-500 dark:text-slate-300">Enable this role.</p>
                    </div>
                </div>
            </div>

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Role saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    @livewire('role.permissions', ['id' => $role->id])

    @livewire('role.member', ['id' => $role->id])
</div>
