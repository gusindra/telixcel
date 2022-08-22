<div>
    <x-jet-action-section>
        <x-slot name="title">2. Payable Account :</x-slot>

        <x-slot name="description">
            {{ __('Company Payable Account.') }}
        </x-slot>

        <!-- Team Action List -->
        <x-slot name="content">
            <div class="flex items-center justify-end text-right">
                <x-jet-action-message class="mr-3" on="added">
                    {{ __('Action added.') }}
                </x-jet-action-message>
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Action saved.') }}
                </x-jet-action-message>
                <x-save-button show="true" wire:click="showCreateModal">
                    {{__('Add Account')}}
                </x-save-button>
            </div>

            <div class="space-y-6">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">

                        @if (count($data['items'])>0)
                            <div class="shadow overflow-hidden border-b border-gray-200 dark:border-slate-700 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 mt-4">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Method</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-11">Provider</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Note</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"> </th>
                                        </tr>
                                    </thead>
                                    <tbody wire:poll class="bg-white dark:bg-slate-600 divide-y divide-gray-200">
                                        @foreach ($data['items'] as $item)
                                            <tr>
                                                <td class="px-6 text-xs py-4 whitespace-no-wrap uppercase align-top">
                                                    {{ $item->method }}
                                                </td>
                                                <td class="px-6 py-4 text-xs whitespace-no-wrap w-1/2">
                                                    <span class="font-bold">{{ $item->provider_name }}</span> -
                                                    <span>{{ $item->provider_location }}</span><br>
                                                    <span class="font-bold">{{ $item->account_name }}</span><br>
                                                    {{ $item->account_number }}
                                                </td>
                                                <td class="px-6 py-4 text-xs whitespace-no-wrap align-top">
                                                    {{ $item->notes }}
                                                </td>
                                                <td class="items-center justify-end py-3 text-right align-top">
                                                    <div class="items-center flex">
                                                        <button title="{{ __('Delete') }}" class="cursor-pointer m-2 w-auto text-sm p-1 bg-red-200 text-gray-800 rounded" wire:click="deleteShowModal('{{ $item->id }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
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
    </x-jet-action-section>

    <!-- Form Input Modal -->
    <x-jet-dialog-modal wire:model="modalVisible">
        <x-slot name="title">
            {{ __('Add Payable Account') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="input.method" value="{{ __('Method') }}" />
                <select
                    name="input.method"
                    id="input.method"
                    class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="input.method"
                    >
                    <option selected>-- Select Method --</option>
                    <option value="transfer">Bank Transfer</option>
                </select>
                <x-jet-input-error for="input.method" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3 grid grid-cols-3">
                <div class=" ">
                    <x-jet-label for="input.provider_name" value="{{ __('Provider Name') }}" />
                    <x-jet-input id="input.provider_name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.provider_name" autofocus />
                    <x-jet-input-error for="input.provider_name" class="mt-2" />
                </div>
                <div class="mx-3">
                    <x-jet-label for="input.account_number" value="{{ __('Account Number') }}" />
                    <x-jet-input id="input.account_number" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.account_number" autofocus />
                    <x-jet-input-error for="input.account_number" class="mt-2" />
                </div>
                <div class="">
                    <x-jet-label for="input.account_name" value="{{ __('Account Name') }}" />
                    <x-jet-input id="input.account_name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.account_name" autofocus />
                    <x-jet-input-error for="input.account_name" class="mt-2" />
                </div>
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="input.provider_location" value="{{ __('Provider Location') }}" />
                <x-jet-input id="input.provider_location" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.provider_location" autofocus />
                <x-jet-input-error for="input.provider_location" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="input.notes" value="{{ __('Notes') }}" />
                <x-jet-input id="input.notes" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.notes" autofocus />
                <x-jet-input-error for="input.notes" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            @if($item_id)
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

    <!-- Remove Input Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingModalRemoval">
        <x-slot name="title">
            {{ __('Remove Confirmation') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this message?') }}<br>
            <b>`{{$account_number}}`</b>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingModalRemoval')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
