<div>
    <!-- <div class="text-right mb-4">
        <a target="_blank" href=" " class="inline-flex items-center px-4 py-2   border   rounded-md font-semibold text-xs   uppercase tracking-widest hover:bg-gray-300 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition">Download</a>
    </div> -->
    <x-jet-form-section submit="update({{$contract->id}})">
        <x-slot name="title">
            {{ __('1. Contract Basic') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Basic information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Title') }}" />
                    <x-jet-input id="name"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="input.title"
                                wire:model.defer="input.title"
                                wire:model.debunce.800ms="input.title" />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="quoteNo" value="{{ __('Email') }}" />
                    <x-jet-input id="quoteNo"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="input.number"
                                wire:model.defer="input.number"
                                wire:model.debunce.800ms="input.number" />
                    <x-jet-input-error for="quoteNo" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="date" value="{{ __('Start Date') }}" />
                    <x-input.date-picker wire:model="input.actived_at" :error="$errors->first('date')"/>
                    <x-jet-input-error for="date" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="price" value="{{ __('End Date') }}" />
                    <x-input.date-picker wire:model="input.expired_at" :error="$errors->first('date')"/>
                    <x-jet-input-error for="price" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Contract saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$contract->id}})">
        <x-slot name="title">
            {{ __('2. Sorce Information') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The source information.') }}
        </x-slot>

        <x-slot name="form">
            @if($addressed)
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="input.model" value="{{ __(' ') }}" />
                    <span class="border rounded-md shadow-sm mt-1 block w-full p-2">{{$input['model']}} : {{$client->name}}</span>
                </div>
            </div>
            @endif
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="input.model" value="{{ __('Source') }}" />
                    <select
                        wire:change="onChangeModel"
                        name="input.model"
                        id="model"
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="input.model"
                        >
                        <option selected>-- Select --</option>
                        <option value="Project">Project</option>
                        <option value="Client">Client</option>

                    </select>
                </div>

                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="type" value="{{ __('Name') }}" />
                    <select
                        wire:change="onChangeModelId"
                        name="input.model_id"
                        id="model_id"
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="input.model_id"
                        >
                        <option selected>-- Select --</option>
                        @foreach($model_list as $key => $item)
                        <option value="{{$key}}">{{$item}}</option>
                        @endforeach

                    </select>
                    <x-jet-input-error for="status" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Quotation saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$contract->id}})">
        <x-slot name="title">
            {{ __('3. File') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Content information.') }}
        </x-slot>

        <x-slot name="form">
            @livewire('image-upload', ['model'=> 'contract', 'model_id'=>$contract->id])
        </x-slot>

        <x-slot name="actions">
        </x-slot>
    </x-jet-form-section>


</div>
