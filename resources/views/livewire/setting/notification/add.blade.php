<div class="p-6">
    <div class="flex items-center justify-end">
        <x-jet-button wire:click="actionShowModal">
            {{__('Add')}}
        </x-jet-button>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('New Announcement') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="input.type" value="{{ __('Type') }}" />
                    <select
                        name="input.type"
                        id="input.type"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="input.type"
                        >
                        <option selected>-- Select --</option>
                        <option value="admin">Admin</option>
                        <option value="app">App</option>
                        <option value="email">Email</option>
                    </select>
                    <x-jet-input-error for="input.province" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="grouptype" value="{{ __('Retriver') }}" />
                    <select
                        name="grouptype"
                        id="grouptype"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="grouptype"
                        >
                        <option selected>-- Select --</option>
                        <option value="role">Role</option>
                        <option value="team">Team</option>
                        <option value="user">User</option>
                    </select>
                    <x-jet-input-error for="grouptype" class="mt-2" />
                </div>
            </div>
            <div class="col-span-12 sm:col-span-1 p-3">
                <x-jet-label for="input.group" value="{{ __('Group') }}" />
                <select
                        name="input.group"
                        id="input.group"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="input.group"
                        multiple
                        >
                        @foreach($lists as $group)
                        <option value="{{$group->id}}">{{$group->name}}</option>
                        @endforeach
                    </select>
                <x-jet-input-error for="input.group" class="mt-2" />
            </div>
            <div class="col-span-12 sm:col-span-1 p-3">
                <x-jet-label for="input.message" value="{{ __('Message') }}" />
                <x-jet-input id="input.message" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.message" autofocus />
                <x-jet-input-error for="input.message" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="resetForm" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="sendAction" wire:loading.attr="disabled">
                {{ __('Send') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>

