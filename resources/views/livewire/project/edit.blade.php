<div>
    <x-jet-form-section submit="update({{$project->id}})">
        <x-slot name="title">
            {{ __('Project') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Project information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Name') }}" />
                    <x-jet-input id="name"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="name"
                                wire:model.defer="name"
                                wire:model.debunce.800ms="name" />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="entity" value="{{ __('Entity of Party B') }}" />
                <select
                    name="entity"
                    id="entity"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="entity"
                    >
                    <option selected>-- Select Party --</option>
                    <option value="mgi">PT MGI</option>
                    <option value="sti">PT STI</option>
                    <option value="telixcel">PT TELIXCEL</option>
                    <option value="mpk">PT MPK</option>
                    <option value="goldenunion">Goldenunion Group</option>
                </select>
                <x-jet-input-error for="entity" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="type" value="{{ __('Type') }}" />
                <select
                    name="type"
                    id="type"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="type"
                    >
                    <option selected>-- Select Type --</option>
                    <option value="selling">Selling Product / Annexed Service</option>
                    <option value="saas">SAAS Service</option>
                    <option value="referral">Referral</option>
                </select>
                <x-jet-input-error for="type" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="type" value="{{ __('Status') }}" />
                <select
                    name="status"
                    id="status"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="status"
                    >
                    <option selected>-- Select Status --</option>
                    <option value="draft">Draft</option>
                    <option value="working_on">Working On</option>
                    <option value="expired">Expired</option>
                    <option value="failed">Failed</option>
                    <option value="active">Active</option>
                    <option value="compleated">Completed</option>
                </select>
                <x-jet-input-error for="status" class="mt-2" />
            </div>

            <!-- is_enabled -->
            <!-- <div class="col-span-6 sm:col-span-6">

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="status" name="status" wire:model="status"
                            wire:model.defer="status" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="status" class="font-medium text-gray-700">is enable ?</label>
                        <p class="text-gray-500">Enable this role.</p>
                    </div>
                </div>
            </div> -->

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Project saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    @livewire('project.add-customer', ['id' => $project->id])
    @livewire('project.edit-type', ['id' => $project->id])



</div>
