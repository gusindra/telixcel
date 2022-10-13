<div>
    <!-- <div class="text-right mb-4">
        <a target="_blank" href=" " class="inline-flex items-center px-4 py-2   border   rounded-md font-semibold text-xs   uppercase tracking-widest hover:bg-gray-300 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition">Download</a>
    </div> -->
    <x-jet-form-section submit="update({{$company->id}})">
        <x-slot name="title">
            {{ __('1. Company Basic') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Basic information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Company ID') }}" />
                    <p>{{$company->id}}</p>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Company Name') }}" />
                    <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model="input.name" wire:model.defer="input.name" wire:model.debunce.800ms="input.name" />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="code" value="{{ __('Code') }}" />
                    <x-jet-input id="code" type="text" class="mt-1 block w-full" wire:model="input.code" wire:model.defer="input.code" wire:model.debunce.800ms="input.code" />
                    <x-jet-input-error for="code" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="tax_id" value="{{ __('NPWP') }}" />
                    <x-jet-input id="tax_id" type="text" class="mt-1 block w-full" wire:model="input.tax_id" wire:model.defer="input.tax_id" wire:model.debunce.800ms="input.tax_id" />
                    <x-jet-input-error for="tax_id" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="person_in_charge" value="{{ __('Person in Charge') }}" />
                    <x-jet-input id="person_in_charge" type="text" class="mt-1 block w-full" wire:model="input.person_in_charge" wire:model.defer="input.person_in_charge" wire:model.debunce.800ms="input.tax_id" />
                    <x-jet-input-error for="person_in_charge" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Company saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    @livewire('setting.company.payment-company-add', ['data' => $company])

    <x-jet-section-border />

    <div class="md:grid md:grid-cols-5 md:gap-6 mt-8 sm:mt-0">
        <div class="md:col-span-1 flex justify-between">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-gray-900 dark:text-slate-300">3. Logo</h3>

                <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">
                    Logo Invoice.
                </p>
            </div>

            <div class="px-4 sm:px-0"></div>
        </div>

        <div class="mt-2 md:mt-0 md:col-span-4">
            <div class="px-4 bg-white dark:bg-slate-600 shadow sm:rounded-tl-md sm:rounded-tr-md">
                <div>
                    @livewire('image-upload', ['model'=> 'company', 'model_id'=>$company->id])
                </div>
            </div>
            <div class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-slate-600 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md"></div>
        </div>
    </div>
</div>
