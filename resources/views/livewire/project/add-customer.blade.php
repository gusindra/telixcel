<div>
    {{-- Party A = the client(s) of this project. Managed via the table below
         (supports many clients). The legacy manual customer fields were removed. --}}
    <x-jet-section-border/>

    <div class="md:grid md:grid-cols-5 md:gap-6 mt-8 sm:mt-0">
        <div class="md:col-span-1 flex justify-between">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-gray-900 dark:text-slate-300">{{ __('Client') }}</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">
                    {{ __('Clients of this project (Party A). A project can have many.') }}
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-4">
            <div class="px-4 py-5 sm:p-6 bg-white dark:bg-slate-600 shadow sm:rounded-lg">
                <div class="flex justify-end items-center mb-3">
                    <x-jet-button wire:click="showClientModal">{{ __('+ Client') }}</x-jet-button>
                </div>

                <livewire:table.project-client :project_id="$project_id" searchable="name, phone, email"
                    exportable :key="'project-client-table-'.$project_id" />
            </div>
        </div>
    </div>


    {{-- Client popup --}}
    <x-jet-dialog-modal wire:model="clientModalVisible">
        <x-slot name="title">{{ __('Add Client to Project') }}</x-slot>

        <x-slot name="content">
            <div class="flex gap-2 mb-4">
                <button type="button" wire:click="$set('mode','existing')"
                        class="px-3 py-1 rounded text-sm {{ $mode==='existing' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    {{ __('Existing Client') }}
                </button>
                <button type="button" wire:click="$set('mode','new')"
                        class="px-3 py-1 rounded text-sm {{ $mode==='new' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    {{ __('New Client') }}
                </button>
            </div>

            @if ($mode === 'existing')
                <div>
                    <x-jet-label for="selectedClient" value="{{ __('Select Client') }}" />
                    <div class="mt-1 flex">
                        <x-searchable-select field="selectedClient" :options="$availableClients" sub="phone"
                            placeholder="{{ __('Search client...') }}" />
                    </div>
                    <x-jet-input-error for="selectedClient" class="mt-2" />
                </div>
            @else
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-jet-label value="{{ __('Title') }}" />
                        <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="newClient.title" placeholder="Mr./PT./CV." />
                    </div>
                    <div>
                        <x-jet-label value="{{ __('PIC / Sender (manual)') }}" />
                        <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="newClient.sender" />
                    </div>
                    <div>
                        <x-jet-label value="{{ __('Name') }}" />
                        <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="newClient.name" />
                        <x-jet-input-error for="newClient.name" class="mt-2" />
                    </div>
                    <div>
                        <x-jet-label value="{{ __('Phone') }}" />
                        <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="newClient.phone" />
                        <x-jet-input-error for="newClient.phone" class="mt-2" />
                    </div>
                    <div class="col-span-2">
                        <x-jet-label value="{{ __('Email') }}" />
                        <x-jet-input type="email" class="mt-1 block w-full" wire:model.defer="newClient.email" />
                        <x-jet-input-error for="newClient.email" class="mt-2" />
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('clientModalVisible')">{{ __('Cancel') }}</x-jet-secondary-button>
            @if ($mode === 'existing')
                <x-jet-button class="ml-2" wire:click="attachExisting">{{ __('Attach') }}</x-jet-button>
            @else
                <x-jet-button class="ml-2" wire:click="createAndAttach">{{ __('Create & Attach') }}</x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>

    {{-- Detach client confirmation modal --}}
    <x-jet-confirmation-modal wire:model="confirmingDetach">
        <x-slot name="title">{{ __('Remove Client') }}</x-slot>
        <x-slot name="content">
            {{ __('Remove') }} <span class="font-semibold">"{{ $detachName }}"</span> {{ __('from this project? The client data itself is not deleted.') }}
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingDetach', false)">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-danger-button class="ml-2" wire:click="detachClient">{{ __('Remove') }}</x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
