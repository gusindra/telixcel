<div class="p-6">
    <x-jet-form-section submit="createTeam">
        <x-slot name="title">
            {{ __('Template Details') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Create a new template to trigger message from customer.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="type" value="{{ __('Type') }}" />
                <select
                    name="type"
                    id="type"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    >
                    <option value="welcome">Welcome</option>
                    <option value="text">Message</option>
                    <option value="question">Question</option>
                    <option value="api">Integration</option>
                </select>

                <!-- Type -->
                <!-- <div class="col-span-6 lg:col-span-4">

                    <x-jet-label for="Type" value="{{ __('Type') }}" />
                    <x-jet-input-error for="Type" class="mt-2" />

                    <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <button
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                            wire:click="$set('templateType', 'message')"
                        >
                            <div class="{{ isset($templateType) && $templateType !== 'message' ? 'opacity-50' : '' }}">

                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $templateType == 'message' ? 'font-semibold' : '' }}">
                                        Message
                                    </div>

                                    @if ($templateType == 'message')
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>


                                <div class="mt-2 text-xs text-gray-600">
                                    description
                                </div>
                            </div>
                        </button>

                        <button
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                            wire:click="$set('templateType', 'question')"
                        >
                            <div class="{{ isset($templateType) && $templateType !== 'question' ? 'opacity-50' : '' }}">

                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $templateType == 'question' ? 'font-semibold' : '' }}">
                                        Question
                                    </div>

                                    @if ($templateType == 'question')
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>


                                <div class="mt-2 text-xs text-gray-600">
                                    description
                                </div>
                            </div>
                        </button>

                        <button
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                            wire:click="$set('templateType', 'api')"
                        >
                            <div class="{{ isset($templateType) && $templateType !== 'api' ? 'opacity-50' : '' }}">

                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $templateType == 'api' ? 'font-semibold' : '' }}">
                                        Integration
                                    </div>

                                    @if ($templateType == 'api')
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>


                                <div class="mt-2 text-xs text-gray-600">
                                    description
                                </div>
                            </div>
                        </button>

                        <button
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                            wire:click="$set('templateType', 'welcome')"
                        >
                            <div class="{{ isset($templateType) && $templateType !== 'welcome' ? 'opacity-50' : '' }}">

                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $templateType == 'welcome' ? 'font-semibold' : '' }}">
                                        Welcome
                                    </div>

                                    @if ($templateType == 'welcome')
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>


                                <div class="mt-2 text-xs text-gray-600">
                                    description
                                </div>
                            </div>
                        </button>
                    </div>
                </div> -->
            </div>
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="name" value="{{ __('Template Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="state.name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="name" value="{{ __('Description') }}" />
                <x-textarea
                    class="mt-1 block w-full"
                    wire:model="request"
                    wire:keydown.enter.prevent="sendMessage"
                />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-button>
                {{ __('Create') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>


    <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
        <x-jet-button wire:click="actionShowModal">
            {{__('Add Action')}}
        </x-jet-button>
    </div>

    <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
        <a class="items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" href="{{route('create.template')}}?id=1">
            {{ __('Add Respond') }}
        </a>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('Action') }}
        </x-slot>

        <x-slot name="content">
            <div>
                <x-jet-label for="email" value="{{ __('Perform Action') }}" />
                <x-textarea
                    class="mt-1 block w-full"
                    placeholder="{{ __('Message') }}"
                    wire:model="request"
                    wire:keydown.enter.prevent="sendMessage"
                    id="message" type="message" name="message"
                    required autofocus
                    :value="old('message')"
                />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalActionVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="createAction" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
