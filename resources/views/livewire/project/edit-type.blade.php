<div>
    <x-jet-section-border />

    <x-jet-form-section submit="save">
        <x-slot name="title">
            {{ __('Type Project') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Information customer or entity of party A.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-6">
                <span class="text-gray-500 font-bold">Type</span>
                @if($project->type == 'selling')
                <span class="text-gray-500 font-bold">Selling Product / Annexed Service</span>
                @elseif ($project->type == 'saas')
                <span class="text-gray-500 font-bold">SAAS Service</span>
                @elseif ($project->type == 'referral')
                <span class="text-gray-500 font-bold">Referral</span>
                @endif
            </div>

            <!-- is_enabled -->
            <div class="col-span-6 sm:col-span-6">
                <div class="flex1 items-start my-2" wire:poll>

                </div>
            </div>

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Type project saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>
</div>
