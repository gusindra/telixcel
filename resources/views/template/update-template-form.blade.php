<x-jet-form-section submit="updateTeamName">
    <x-slot name="title">
        {{ __('Template Name') }}
    </x-slot>

    <x-slot name="description">
        {{ __('The template\'s name and information.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 grid grid-cols-2">
            <!-- Type Information -->
            <div class="col-span-1">
                <x-jet-label value="{{ __('Type') }}" />

                <div class="flex items-center mt-2"><div class="ml-4 leading-tight">
                        <div>{{ $template->type }}</div>
                        <div class="text-gray-700 text-sm">Explaination</div>
                    </div>
                </div>
            </div>

            <!-- Template Name -->
            <div class="col-span-1 sm:col-span-1">
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
        </div>

        <!-- Template Description -->
        <div class="col-span-6 sm:col-span-6">
            <x-jet-label for="description" value="{{ __('Description') }}" />

            <x-textarea wire:model="description"
                        wire:model.defer="description"
                        value="description" class="mt-1 block w-full" :disabled="! Gate::check('update', $template)"></x-textarea>
            <x-jet-input-error for="name" class="mt-2" />
        </div>
    </x-slot>

    @if (Gate::check('update', $template))
        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    @endif
</x-jet-form-section>
