<div>
    <!-- Generate API Token -->
    <x-jet-action-section>
        <x-slot name="title">
            {{ __('Create Webhook') }}

        </x-slot>

        <x-slot name="description">
            {{ __('Webhook to retrive all message and store to Telixcel.') }}
            <hr><br><br>
            <!-- == > {{base64_encode('Wademoid')}} < == -->
        </x-slot>

        <x-slot name="content">
            <div class="flex items-center justify-end text-right mb-3">
                <x-jet-action-message class="mr-3" on="added">
                    {{ __('Action added.') }}
                </x-jet-action-message>
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Action saved.') }}
                </x-jet-action-message>
                <x-jet-button wire:click="actionShowModal">
                    {{__('Create')}}
                </x-jet-button>
            </div>

            <div class="space-y-6">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            @if ($data->count())
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:bg-slate-600">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Webhook</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Status</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-slate-700 divide-y divide-gray-200">
                                        @foreach ($data as $item)
                                            <tr>
                                                <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                    <x-jet-label for="client" value="{{ $item->client }}" />
                                                    <input value="{{ route('api.client.webhook', $item->slug) }}" class="bg-gray-200 dark:bg-slate-800 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm mt-1 block w-full p-3" readonly />
                                                </td>
                                                <td>
                                                    <div>
                                                        @if($item->is_enabled==1)
                                                        <svg class="h-5 w-5 stroke-current text-green-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        @else
                                                        <svg class="h-5 w-5 stroke-current text-red-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                                                    <div class="flex items-center">
                                                        <button class="cursor-pointer ml-6 text-sm text-red-500 py-6" wire:click="updateShowModal('{{ $item->id }}')">
                                                            {{ __('Update') }}
                                                        </button>
                                                        <button class="cursor-pointer ml-6 text-sm text-red-500 py-6" wire:click="deleteShowModal('{{ $item->id }}')">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-jet-form-section>


    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('Action message') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="client" value="{{ __('Client Provider') }}" />
                <select
                    name="client"
                    id="client"
                    class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="client"
                    >
                    <option selected>-- Select Client --</option>
                    <option value="twilio">twilio.com</option>
                    <option value="macrokiosk">Chat API Mk</option>
                    <option value="api_sms_mk">SMS API Mk</option>
                    <!-- <option value="messagebird">messagebird.com</option> -->
                </select>
                <x-jet-input-error for="client" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="name" value="{{ __('API Key') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="api_key" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="name" value="{{ __('Server Key') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="server_key" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="name" value="{{ __('Credential') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="credential" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <!-- is_enabled -->
            <div class="col-span-6 sm:col-span-6 p-3">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_enabled" name="is_enabled" wire:model="is_enabled"
                            wire:model.defer="is_enabled" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_enabled" class="font-medium text-gray-700 dark:text-slate-300">Is enable ?</label>
                        <p class="text-gray-500 dark:text-slate-300">Tick checkbox to enable this service.</p>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalActionVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            @if($data_id)
            <x-jet-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
            @else
            <x-jet-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
                {{ __('Add') }}
            </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Remove Action Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingActionRemoval">
        <x-slot name="title">
            {{ __('Remove Confirmation') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this webhook?') }}<br>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingActionRemoval')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
