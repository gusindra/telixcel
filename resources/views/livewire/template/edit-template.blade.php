<div>
    @if($template->template_id)
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="bg-indigo-600 mb-4">
            <div class="w-full mx-auto p-3 px-3">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center">
                        <span class="flex p-2 rounded-lg bg-indigo-800">
                        <!-- Heroicon name: outline/speakerphone -->
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        </span>
                        <p class="ml-3 font-medium text-white truncate">
                        <span class="md:hidden">
                            Respond Question
                        </span>
                        <span class="hidden md:inline">
                            This template is use for respond question `{{$template->question->name}}`.
                        </span>
                        </p>
                    </div>
                    <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                        <a href="{{route('show.template', $template->question->uuid)}}" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                            See Question
                        </a>
                    </div>
                    <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                        <button type="button" class="-mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2">
                    </button></div>
                </div>
            </div>
        </div>
    @endif

    {{-- Template Information --}}
    <x-jet-form-section submit="updateTemplate">
        <x-slot name="title">
            {{ __('Template') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The template\'s name and information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 grid grid-cols-5">
                <!-- Type Information -->
                <div class="col-span-2">
                    <x-jet-label class="capitalize" value="{{ $template->type }}" />

                    <div class="flex items-center mt-1">
                        <div class="p-3 pl-0 pt-0">
                            <div class="border border-lightgrey-400 p-1 rounded-lg">
                                <div class="text-gray-700 dark:text-slate-300 text-sm ">
                                    @if($template->type == 'api')
                                        All Respond will check via API form endpoint that given.
                                    @elseif($template->type == 'welcome')
                                        Send a welcome message in the first time.
                                    @elseif($template->type == 'text')
                                        Sent message base on the text/keyword send by customer.
                                    @elseif($template->type == 'question')
                                        Sent message helper/question for customer.
                                    @elseif($template->type == 'error')
                                        Sent error message to customer.
                                    @elseif($template->type == 'helper')
                                        Helper template for agent.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-1">
                    <x-jet-label for="name" value="{{ __('Created By') }}" />
                    <div class="flex items-center mt-1">
                        <div class="p-3 pl-0 pt-0">
                            <div class="p-1 rounded-lg">
                                <div class="text-gray-700 dark:text-slate-300 text-sm capitalize">
                                    {{$template->createdBy->name}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-1">
                    <x-jet-label for="name" value="{{ __('Created At') }}" />
                    <div class="flex items-center mt-1">
                        <div class="p-3 pl-0 pt-0">
                            <div class="p-1 rounded-lg">
                                <div class="text-gray-700 dark:text-slate-300 text-sm ">
                                    {{$template->created_at->format('d F Y')}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-1">
                    <x-jet-label for="name" value="{{ __('Updated At') }}" />
                    <div class="flex items-center mt-1">
                        <div class="p-3 pl-0 pt-0">
                            <div class="p-1 rounded-lg">
                                <div class="text-gray-700 dark:text-slate-300 text-sm ">
                                {{$template->updated_at->format('d F Y')}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Template Name -->
            <div class="col-span-6 sm:col-span-6">
                <x-jet-label for="name" value="{{ __('Name') }}" />
                <x-jet-input id="name"
                            type="text"
                            class="mt-1 block w-full"
                            wire:model="name"
                            wire:model.defer="name"
                            wire:model.debunce.800ms="name"
                            :disabled="! Gate::check('update', $template)" />
                <x-jet-input-error for="name" class="mt-2" />
            </div>

            <!-- Template Description -->
            <div class="col-span-6 sm:col-span-6">
                <x-jet-label for="description" value="{{ __('Description') }}" />

                <x-textarea wire:model="description"
                            wire:model.defer="description"
                            value="description" class="mt-1 block w-full" :disabled="! Gate::check('update', $template)"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
            </div>

            <!-- is_enabled -->
            <div class="col-span-6 sm:col-span-6">

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_enabled" name="is_enabled" wire:model="is_enabled"
                            wire:model.defer="is_enabled" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_enabled" class="font-medium text-gray-700 dark:text-slate-300">is enable ?</label>
                        <p class="text-gray-500 dark:text-slate-300">Enable template to send respond to your customer.</p>
                    </div>
                </div>
            </div>

            @if($template->type!='helper')
            <!-- is_wait_for_chat -->
            <div class="col-span-6 sm:col-span-6">

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_waiting" name="is_waiting" wire:model="is_waiting"
                            wire:model.defer="is_waiting" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_waiting" class="font-medium text-gray-700 dark:text-slate-300">is waiting Agent Response ?</label>
                        <p class="text-gray-500 dark:text-slate-300">Enable to notif agent to give response to the customer.</p>
                    </div>
                </div>
            </div>
            @endif

        </x-slot>

        @if (Gate::check('update', $template))
            <x-slot name="actions">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Template saved.') }}
                </x-jet-action-message>

                <x-jet-button>
                    {{ __('Save') }}
                </x-jet-button>
            </x-slot>
        @endif
    </x-jet-form-section>

    @if($template->type!='welcome' && $template->type!='helper')
    @livewire('template.edit-trigger', ['template' => $template])
    @endif

    @livewire('template.add-action', ['template' => $template])

    @if($template->type=='api')
    @livewire('template.edit-api', ['template' => $template])
    @livewire('template.add-respond-api', ['template' => $template])
    @endif

    @if($template->type=='question')
    @livewire('template.edit-answer', ['template' => $template])
    @endif

    @if($template->type!='error' && $template->type!='text' && $template->type!='welcome' && $template->type!='helper')
    @livewire('template.add-error', ['template' => $template])
    @endif

</div>
