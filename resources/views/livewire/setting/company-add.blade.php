<div class="p-6">
    <div class="flex items-center justify-end">
        <x-jet-button wire:click="actionShowModal">
            {{__('Add Company')}}
        </x-jet-button>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('New Company') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="input.name" value="{{ __('Company Name') }}" />
                    <x-jet-input id="input.name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.name" autofocus />
                    <x-jet-input-error for="input.name" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="input.code" value="{{ __('Code') }}" />
                    <x-jet-input id="input.code" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.code" autofocus />
                    <x-jet-input-error for="input.code" class="mt-2" />
                </div>
            </div>
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="input.tax_id" value="{{ __('Tax No / NPWP') }}" />
                    <x-jet-input id="input.tax_id" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.tax_id" autofocus />
                    <x-jet-input-error for="input.tax_id" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="input.post_code" value="{{ __('Post Code') }}" />
                    <x-jet-input id="input.post_code" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.post_code" autofocus />
                    <x-jet-input-error for="input.post_code" class="mt-2" />
                </div>
            </div>
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="input.province" value="{{ __('Province') }}" />
                    <x-jet-input id="input.province" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.province" autofocus />
                    <x-jet-input-error for="input.province" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="input.city" value="{{ __('City') }}" />
                    <x-jet-input id="input.city" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.city" autofocus />
                    <x-jet-input-error for="input.city" class="mt-2" />
                </div>
            </div>
            <div class="col-span-12 sm:col-span-1 p-3">
                <x-jet-label for="input.address" value="{{ __('Address') }}" />
                <x-jet-input id="input.address" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.address" autofocus />
                <x-jet-input-error for="input.address" class="mt-2" />
            </div>
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 p-3">
                    <x-jet-label for="input.person_in_charge" value="{{ __('Person in Charge') }}" />
                    <x-jet-input id="input.person_in_charge" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="input.person_in_charge" autofocus />
                    <x-jet-input-error for="input.person_in_charge" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalActionVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>

