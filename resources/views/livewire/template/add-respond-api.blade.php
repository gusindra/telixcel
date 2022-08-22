<div>
    <x-jet-section-border />

    <!-- Manage List Action -->
    <div class="mt-10 sm:mt-0">
        <x-jet-action-section>
            <x-slot name="title">
                {{ __('Add Respond from API') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Add a respond to from template API.') }}
            </x-slot>

            <!-- Team Action List -->
            <x-slot name="content">
                <div class="flex items-center justify-end text-right">
                    <x-jet-action-message class="mr-3" on="added">
                        {{ __('Answer added.') }}
                    </x-jet-action-message>
                    <x-jet-action-message class="mr-3" on="saved">
                        {{ __('Answer saved.') }}
                    </x-jet-action-message>
                    @if($selectionTemplate)
                    <x-jet-button wire:click="selectionShowModal">
                        {{__('Add respond answers')}}
                    </x-jet-button>
                    @else
                    <x-jet-button wire:click="createShowModal">
                        {{__('Add respond answers')}}
                    </x-jet-button>
                    @endif
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
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Status Code</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Template</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-slate-700 divide-y divide-gray-200">
                                                @foreach ($data as $item)
                                                    <tr>
                                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                           {{ $item->trigger }}
                                                        </td>
                                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                            <a href="{{route('show.template', $item->uuid)}}" class="underline p-2 border border-transparent text-xs font-medium rounded-md {{$item->is_enabled==1?'text-indigo-700 bg-indigo-200 hover:bg-indigo-200':'text-red-700 bg-red-100 hover:bg-red-200'}}" >
                                                                {{ $item->name }}
                                                            </a>
                                                        </td>
                                                        <td class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                                                            <div class="flex items-center">
                                                                <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="deleteShowModal('{{ $item->id }}')">
                                                                    {{ __('Remove') }}
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

    <!-- Form Create Modal -->
    <x-jet-dialog-modal wire:model="modalCreateVisible">
        <x-slot name="title">
            {{ __('Create new Respond template') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="type" value="{{ __('Type') }}" />
                <select
                    name="type"
                    id="type"
                    class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="type"
                    >
                    <option selected>-- Select Type --</option>
                    <option value="text">Message</option>
                    <option value="question">Question</option>
                    <option value="api">Integration</option>
                </select>
                <x-jet-input-error for="type" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="name" value="{{ __('Template Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="description" value="{{ __('Description') }}" />
                <x-jet-input id="description" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="description" autofocus />
                <x-jet-input-error for="description" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="trigger" value="{{ __('Respond Code') }}" />
                <x-jet-input id="trigger" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="trigger" placeholder="" autofocus />
                <p class="text-sm text-gray-500">This code will get from json respond. Ex: 0/1/error/success</p>
                <x-jet-input-error for="description" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalCreateVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            <x-jet-secondary-button wire:click="selectionShowModal">
                {{__('Select From Template')}}
            </x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
                {{ __('Add') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Form Selection Modal -->
    <x-jet-dialog-modal wire:model="modalSelectionVisible">
        <x-slot name="title">
            {{ __('Select Respond template') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                @if($selectionTemplate)
                    <x-jet-label for="selectedTemplate" value="{{ __('Template') }}" />
                    <select
                        name="selectedTemplate"
                        id="selectedTemplate"
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="selectedTemplate"
                        >
                        <option selected>-- Select Template --</option>
                        @foreach($selectionTemplate as $template)
                        <option value="{{$template->id}}">{{$template->name}}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="selectedTemplate" class="mt-2" />
                @else
                    <div>
                        No Template, you can try to create new template.
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalSelectionVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            <x-jet-secondary-button wire:click="createShowModal">
                {{__('Create new Template')}}
            </x-jet-secondary-button>
            @if($selectionTemplate)
            <x-jet-button class="ml-2" wire:click="selectTemplate" wire:loading.attr="disabled">
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
            {{ __('Are you sure you would like to remove this respond?') }}<br>
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
