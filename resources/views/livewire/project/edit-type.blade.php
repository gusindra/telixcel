<div>
    <x-jet-section-border />
    @if($project->type == 'selling')
        <span class="text-gray-500 dark:text-slate-300 font-bold">Selling Product / Annexed Service</span>
        @livewire('commercial.product-lines', ['model' => 'project', 'data' => $project, 'disabled' => $disabled])
    @elseif ($project->type == 'saas')
        <span class="text-gray-500 dark:text-slate-300 font-bold">SAAS Service</span>
        @livewire('commercial.product-lines', ['model' => 'project', 'data' => $project, 'disabled' => $disabled ])
    @elseif ($project->type == 'referral')
        <x-jet-form-section submit="save">
            <x-slot name="title">
                {{ __('Type Project') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Information type and source project.') }}
            </x-slot>

            <x-slot name="form">
                @if(in_array($project->status, ['submit','approved', 'done']))
                    <div class="col-span-12 sm:col-span-12">
                        @if ($project->type == 'referral')
                            <span class="text-gray-500 dark:text-slate-300font-bold">This project referrer from</span>
                            <p class="capitalize">{{$referrer_name}}</p>
                        @elseif($project->product_line)
                            <span class="text-gray-500 dark:text-slate-300 font-bold">Product Line</span>
                            <p class="capitalize">{{$project->productLine->name}}</p>
                        @endif
                    </div>
                @else
                    <div class="col-span-12 sm:col-span-12">

                            <span class="text-gray-500 dark:text-slate-300 font-bold">This project referrer from</span>
                            <div class="col-span-12 sm:col-span-1">
                                <x-jet-input id="referrer_name"
                                        disabled="{{disableInput($project->status)}}"
                                        type="text"
                                        class="mt-1 block w-full"
                                        wire:model="referrer_name"
                                        wire:model.defer="referrer_name"
                                        wire:model.debunce.800ms="referrer_name" />
                                <x-jet-input-error for="referrer_name" class="mt-2" />
                            </div>

                    </div>

                    <!-- is_enabled -->
                    <div class="col-span-6 sm:col-span-6">
                        <div class="flex1 items-start my-2" wire:poll>

                        </div>
                    </div>
                @endif
            </x-slot>

            <x-slot name="actions">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Type project saved.') }}
                </x-jet-action-message>
                @if ($project->type == 'referral')
                <x-jet-button on="true">
                    {{ __('Save') }}
                </x-jet-button>
                @endif
            </x-slot>
        </x-jet-form-section>
    @endif
</div>
