<div>
    <!-- <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" href="{{route('create.template')}}">
        {{ __('Create') }}
    </a> -->
    <div class="flex items-center p-4 text-right">
        <a wire:click="actionShowModal" class="inline-flex cursor-pointer items-center px-2 py-1 bg-green-800 border border-transparent rounded-md font-normal text-xs text-white 1g-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="actionShowModal">
            {{__('+ Project')}}
        </a>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('New Project') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="type" value="{{ __('Type') }}" />
                <select
                    name="type"
                    id="type"
                    class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="type"
                    >
                    <option selected>-- Select Type --</option>
                    <option value="selling">Selling Product / Annexed Service</option>
                    <option value="saas">SAAS Service</option>
                    <option value="referral">Referral</option>
                </select>
                <x-jet-input-error for="type" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="name" value="{{ __('Project Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="entity" value="{{ __('Entity of Party B') }}" />
                <select
                    name="entity"
                    id="entity"
                    class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="entity"
                    >
                    <option selected>-- Select Party --</option>
                    @foreach ($companies as $company)
                        <option value="{{$company->id}}">{{$company->name}}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="entity" class="mt-2" />
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

