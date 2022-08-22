<div>
    <x-jet-action-section>
        <x-slot name="title"></x-slot>

        <x-slot name="description">
            {{ __('Add a input for request data to your endpoint.') }}
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
                <x-jet-button wire:click="showCreateModal">
                    {{__('Add Input')}}
                </x-jet-button>
            </div>

            <div class="space-y-6">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            @if ($data->count())
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 mt-2">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Input Name</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-slate-700 divide-y divide-gray-200">
                                            @foreach ($data as $item)
                                                <tr>
                                                    <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                        {{ $item->name }}
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

    <!-- Form Input Modal -->
    <x-jet-dialog-modal wire:model="modalVisible">
        <x-slot name="title">
            {{ __('Form Input') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="name" value="{{ __('Input Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            @if($input_id)
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
            <b>`{{$name}}`</b>
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
