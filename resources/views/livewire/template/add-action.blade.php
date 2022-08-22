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
                                    <table class="min-w-full divide-y divide-gray-200 mt-2">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Message</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-slate-700 divide-y divide-gray-200">
                                            @foreach ($data as $item)
                                                <tr>
                                                    <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                        <div class="w-full flex md:flex-col bg-gradient-to-br from-green-100 to-green-200 rounded-tr-2xl rounded-b-xl">
                                                            <div class="items-start relative z-10 p-3 xl:p-3">
                                                                @if($item->type=='image')
                                                                    <img src="{{$item->message}}" class="bg-gray-100 max-w-400 min-w-1/2 right-0 order-last" />
                                                                @elseif($item->type=='document')
                                                                    <div class="flex justify-between">
                                                                        <div><img src="{{url('/backend/img/'.substr(strrchr($item->message, '.'), 1).'.png')}}" class="h-8" /></div>
                                                                        <div class="text-left mt-1">{{substr(strrchr($item->message, '/'), 1)}}</div>
                                                                        <div><a href="{{$item->message}}" data-testid="audio-download" data-icon="audio-download" class=""><svg viewBox="0 0 34 34" width="34" height="34" class=""><path fill="currentColor" d="M17 2c8.3 0 15 6.7 15 15s-6.7 15-15 15S2 25.3 2 17 8.7 2 17 2m0-1C8.2 1 1 8.2 1 17s7.2 16 16 16 16-7.2 16-16S25.8 1 17 1z"></path><path fill="currentColor" d="M22.4 17.5h-3.2v-6.8c0-.4-.3-.7-.7-.7h-3.2c-.4 0-.7.3-.7.7v6.8h-3.2c-.6 0-.8.4-.4.8l5 5.3c.5.7 1 .5 1.5 0l5-5.3c.7-.5.5-.8-.1-.8z"></path></svg></a></div>
                                                                    </div>
                                                                @elseif($item->type=='video')
                                                                    <video width="320" height="240" controls>
                                                                        <source src="{{$item->message}}" type="video/mp4">
                                                                        <source src="{{$item->message}}" type="video/ogg">
                                                                        Your browser does not support the video tag.
                                                                    </video>
                                                                @elseif($item->type=='audio')
                                                                    <audio controls>
                                                                        <source src="{{$item->message}}" type="audio/ogg">
                                                                        <source src="{{$item->message}}" type="audio/mpeg">
                                                                        Your browser does not support the audio element.
                                                                    </audio>
                                                                @else
                                                                    <span class="whitespace-pre-wrap dark:text-slate-600">{{ $item->message }}</span>
                                                                @endif
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


            <div x-data="{toggle: '{{$type}}'}">

                <div class="flex justify-center items-center mt-8">
                    <span class=" block mr-3 text-xs">Text</span>

                    <div class="relative rounded-full w-12 h-7 transition duration-200 ease-linear"
                        :class="[toggle ? 'bg-black' : 'bg-black']">

                        <label for="toggle"
                            class="mt-1 absolute drop-shadow-lg left-1 bg-white w-5 h-5 rounded-full transition transform duration-100 ease-linear cursor-pointer"
                            :class="[toggle ? 'translate-x-full border-black' : 'translate-x-0 border-gray-400']">
                        </label>

                        <input type="checkbox" value="1"
                            wire:model.defer="type"
                            x-ref="type"
                            id="toggle"
                            name="toggle"
                            x-model="toggle"
                            class="hidden dark:bg-slate-800 appearance-none w-full h-full active:outline-none focus:outline-none" />
                    </div>

                    <span class=" block ml-3 text-xs">Attachment</span>
                </div>

                <div id="toggleOff" x-show="!toggle">
                    <div class="col-span-6 sm:col-span-4 p-3">
                        <x-jet-label for="photo" value="{{ __('Message') }}" />
                        <x-textarea role="textbox"
                                    contenteditable wire:model="message"
                                    wire:model.defer="message"
                                    value="message" class="mt-1 block w-full" :disabled="! Gate::check('update', $template)" placeholder="message"></x-textarea>
                        <x-jet-input-error for="name" class="mt-2" />
                    </div>
                </div>

                <div id="toggleOn" x-show="toggle">
                    <x-jet-label for="photo" value="{{ __('Link') }}" />
                    <input class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 w-full my-1 text-sm" type="text" placeholder="{{ __('Link Attachment') }}"
                                x-ref="link_attachment"
                                wire:model.defer="link_attachment">
                </div>

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
