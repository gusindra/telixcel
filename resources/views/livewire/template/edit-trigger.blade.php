<div>
    <x-jet-section-border />
    {{--Trigger Condition--}}
    @if($template->type=='error')
    <x-jet-action-section submit="updateTrigger">
        <x-slot name="title">
            {{ __('Condition Response') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Triger will send if fail to awnser Template.') }}
        </x-slot>

        <x-slot name="content">
            <!-- Trigger Condition -->
            <div class="space-y-6">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">

                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">
                                            Error Template For
                                            </th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-slate-700 divide-ydivide-gray-200">

                                        @foreach($template->questionError as $question)
                                            <tr>
                                                <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                                    <a href="{{route('show.template', $question->uuid)}}" class="underline p-2 border border-transparent text-xs font-medium rounded-md {{$question->is_enabled==1?'text-indigo-700 bg-indigo-200 hover:bg-indigo-200':'text-red-700 bg-red-100 hover:bg-red-200'}}" >
                                                        {{$question->name}}
                                                    </a>
                                                </td>
                                                <td class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                                                    <div class="flex items-center"></div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-jet-action-section>
    @elseif ($template->question && $template->question->type=='api')
    <x-jet-form-section submit="updateTrigger">
        <x-slot name="title">
            {{ __('Condition Response') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Triger / response from customer before call action.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Trigger Word -->
            <div class="col-span-4 sm:col-span-4">
                <div>
                    <x-jet-label for="trigger" value="{{ __('Status Code') }}" />
                </div>
                <x-jet-input id="trigger"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="trigger"
                                wire:model.defer="trigger"
                                wire:model.debunce.800ms="trigger"
                                :disabled="! Gate::check('update', $template)" />

                <x-jet-input-error for="trigger" class="mt-2" />
            </div>
        </x-slot>

        @if (Gate::check('update', $template))
            <x-slot name="actions">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Trigger saved.') }}
                </x-jet-action-message>

                <x-jet-button>
                    {{ __('Save') }}
                </x-jet-button>
            </x-slot>
        @endif
    </x-jet-form-section>
    @else
    <x-jet-form-section submit="updateTrigger">
        <x-slot name="title">
            {{ __('Condition Response') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Triger / response from customer before call action.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Trigger Condition -->
            <div class="col-span-2 sm:col-span-2">
                <div>
                    <x-jet-label for="trigger" value="{{ __('Condition') }}" />
                </div>
                <select
                    name="trigger_condition"
                    id="trigger_condition"
                    class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="trigger_condition"
                    >
                    <option value="equal" selected>Equals</option>
                    <!--<option value="no_equal">Not equal</option>-->
                    <option value="contain">Contains</option>
                    <!--<option value="no_contain">Does not contain</option>-->
                </select>

                <x-jet-input-error for="trigger_condition" class="mt-2" />
            </div>
            <!-- Trigger Word -->
            <div class="col-span-4 sm:col-span-4">
                <div>
                    <x-jet-label for="trigger" value="{{ __('Trigger (Keyword)') }}" />
                </div>
                <x-jet-input id="trigger"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="trigger"
                                wire:model.defer="trigger"
                                wire:model.debunce.800ms="trigger"
                                :disabled="! Gate::check('update', $template)" />
                <p class="text-sm text-gray-500">These is a word that will trigger the action (`0`,`1`,`2`,`3`, `/help` etc).</p>

                <x-jet-input-error for="trigger" class="mt-2" />
            </div>
        </x-slot>

        @if (Gate::check('update', $template))
            <x-slot name="actions">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Trigger saved.') }}
                </x-jet-action-message>

                <x-jet-button>
                    {{ __('Save') }}
                </x-jet-button>
            </x-slot>
        @endif
    </x-jet-form-section>
    @endif
</div>
