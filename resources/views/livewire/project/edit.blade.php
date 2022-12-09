<div>
    <x-jet-form-section submit="update({{$project->id}})">
        <x-slot name="title">
            {{ __('Project') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Project information.') }}
        </x-slot>

        <x-slot name="form">
            @if($project->status=='approved')
                <div class="col-span-6 grid grid-cols-2">
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-label for="name" value="{{ __('Name') }}" />
                        <p>{{$project->name}}</p>
                    </div>
                </div>
                <div class="col-span-6 grid grid-cols-2">
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-label for="name" value="{{ __('Entity of Party B') }}" />
                        <p class="uppercase">{{$entity > 0 ? $project->company ? $project->company->name:$entity :$entity}}</p>
                    </div>
                </div>
                <div class="col-span-6 grid grid-cols-2">
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-label for="name" value="{{ __('Type') }}" />
                        <p class="capitalize">{{$project->type}}</p>
                    </div>
                </div>
            @else
                <div class="col-span-6 grid grid-cols-2">
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-label for="name" value="{{ __('Name') }}" />
                        <x-jet-input
                            disabled="{{disableInput($project->status)}}"
                            id="name"
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
                        {{disableInput($project->status)?'disabled':''}}
                        name="entity"
                        id="entity"
                        class="border p-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:bg-slate-800 dark:text-slate-300 mt-1 block w-full"
                        wire:model.debunce.800ms="entity"
                        >
                        <option selected>-- Select Party --</option>
                        @foreach (get_my_companies() as $company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="entity" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4">
                    <x-jet-label for="type" value="{{ __('Type') }}" />
                    <select
                        {{disableInput($project->status)?'disabled':''}}
                        name="type"
                        id="type"
                        class="border p-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:bg-slate-800 dark:text-slate-300 mt-1 block w-full"
                        wire:model.debunce.800ms="type"
                        >
                        <option selected>-- Select Type --</option>
                        <option value="selling">Selling Product / Annexed Service</option>
                        <option value="saas">SAAS Service</option>
                        <option value="referral">Referral</option>
                    </select>
                    <x-jet-input-error for="type" class="mt-2" />
                </div>
            @endif

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3 xs-text" on="saved">
                {{ __('Project saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{$project->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

</div>
