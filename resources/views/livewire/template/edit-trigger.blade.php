<div>
    <x-jet-section-border />

    {{--Trigger Condition--}}
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
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="trigger_condition"
                    >
                    <option value="equal" selected>Equals</option>
                    <option value="no_equal">Not equal</option>
                    <option value="contain">Contains</option>
                    <option value="no_contain">Does not contain</option>
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
</div>
