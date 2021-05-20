<div>
    <x-jet-section-border />

    <!-- Manage List Action -->
    <div class="mt-10 sm:mt-0">
        <x-jet-action-section>
            <x-slot name="title">
                {{ __('Add Action Message') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Add a action message to customer.') }}
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
                    <x-jet-button wire:click="actionShowModal">
                        {{__('Add Action')}}
                    </x-jet-button>
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                @if ($data->count())
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Message</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($data as $item)
                                                <tr>
                                                    <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                        <div class="w-full flex md:flex-col bg-gradient-to-br from-green-100 to-green-200 rounded-tr-2xl rounded-tr-2xl rounded-b-xl">
                                                            <div class="sm:max-w-sm sm:flex-none md:w-auto md:flex-auto flex flex-col items-start relative z-10 p-3 xl:p-3">
                                                                <span class="whitespace-pre-wrap">{{ $item->message }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                                                        <div class="flex items-center">
                                                            <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="updateShowModal('{{ $item->id }}')">
                                                                {{ __('Update') }}
                                                            </button>
                                                            <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="deleteShowModal('{{ $item->id }}')">
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
        </x-jet-action-section>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('Action message') }}
        </x-slot>

        <x-slot name="content">
            @if($template->question && $template->question->type == 'api')
                <div class="col-span-12 sm:col-span-12">
                    <div class="ml-3 text-sm">
                        <input id="is_multidata" name="is_multidata" wire:model="is_multidata" wire:model.defer="is_multidata" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"><label for="is_enabled" class="font-medium text-gray-500 px-2"> Enable if this action is used for looping result data respond.</label>
                    </div>
                    @if ($is_multidata)
                        <x-jet-input placeholder="[data][resultList]" id="array_data" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="array_data" autofocus />
                    @endif
                </div>
                <br>

                @livewire('template.add-data-action', ['actionId' => $actionId], key($actionId))
            @endif
            <div class="col-span-6 sm:col-span-4 p-3">

                <x-textarea role="textbox"
                            contenteditable wire:model="message"
                            wire:model.defer="message"
                            value="message" class="mt-1 block w-full" :disabled="! Gate::check('update', $template)" placeholder="message"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
            </div>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalActionVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            @if($actionId)
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
            {{ __('Are you sure you would like to remove this message?') }}<br>
            <b>`{{$message}}`</b>
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
