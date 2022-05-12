<div>
    <x-jet-section-border />

    <x-jet-form-section submit="save">
        <x-slot name="title">
            {{ __('Type Project') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Information type and source project.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-6">
                @if($project->type == 'selling')
                    <span class="text-gray-500 font-bold">Selling Product / Annexed Service</span>
                    @livewire('commercial.product-lines', ['model' => 'project', 'data' => $project])
                @elseif ($project->type == 'saas')
                    <span class="text-gray-500 font-bold">SAAS Service</span>
                    @livewire('commercial.product-lines', ['model' => 'project', 'data' => $project])
                @elseif ($project->type == 'referral')
                    <span class="text-gray-500 font-bold">This project referrer from</span>
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-input id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    wire:model="name"
                                    wire:model.defer="name"
                                    wire:model.debunce.800ms="name" />
                        <x-jet-input-error for="name" class="mt-2" />
                    </div>
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
            @if ($project->type == 'referral')
            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
            @endif
        </x-slot>
    </x-jet-form-section>
</div>
