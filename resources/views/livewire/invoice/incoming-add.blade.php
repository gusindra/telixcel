<div>
    <a wire:click="actionShowModal"
       class="inline-flex cursor-pointer items-center px-3 py-2 bg-amber-600 border border-transparent rounded-md font-normal text-xs text-white hover:bg-amber-700 transition">
        {{ __('+ Invoice Masuk') }}
    </a>

    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">{{ __('Upload Invoice Masuk (Vendor)') }}</x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-jet-label value="{{ __('Invoice Code') }}" />
                    <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="code" />
                    <x-jet-input-error for="code" class="mt-2" />
                </div>
                <div>
                    <x-jet-label value="{{ __('Vendor') }}" />
                    <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="vendor_name" />
                    <x-jet-input-error for="vendor_name" class="mt-2" />
                </div>
                <div>
                    <x-jet-label value="{{ __('Amount') }}" />
                    <x-jet-input type="number" class="mt-1 block w-full" wire:model.defer="amount" />
                    <x-jet-input-error for="amount" class="mt-2" />
                </div>
                <div>
                    <x-jet-label value="{{ __('Invoice Date') }}" />
                    <x-jet-input type="date" class="mt-1 block w-full" wire:model.defer="invoice_date" />
                    <x-jet-input-error for="invoice_date" class="mt-2" />
                </div>
                <div class="col-span-2">
                    <x-jet-label value="{{ __('Description') }}" />
                    <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="description" />
                </div>
                <div class="col-span-2">
                    <x-jet-label value="{{ __('Invoice File (PDF / image)') }}" />
                    <input type="file" wire:model="file"
                           class="mt-1 block w-full text-sm dark:text-slate-300" />
                    <div wire:loading wire:target="file" class="text-xs text-gray-400 mt-1">{{ __('Uploading...') }}</div>
                    <x-jet-input-error for="file" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalActionVisible')">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="create" wire:loading.attr="disabled">{{ __('Save') }}</x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
