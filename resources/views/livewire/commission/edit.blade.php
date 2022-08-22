<div>
    <x-jet-form-section submit="update({{$master->id}})">
        <x-slot name="title">
            {{ __('Commission') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Commission details.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 grid grid-cols-4">
            <div class="col-span-12 sm:col-span-1 mr-4">
                    <x-jet-label for="clientId" value="{{ __('Agent') }}" />
                    <select
                        {{disableInput($master->status)?'disabled':''}}
                        name="clientId"
                        id="clientId"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="clientId"
                        >
                        <option selected>-- Select --</option>
                        <option value="0">All Agent</option>
                        @foreach($model_list as $key => $item)
                        <option value="{{$key}}">{{$item}}</option>
                        @endforeach

                    </select>
                    <x-jet-input-error for="clientId" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 mr-4">
                    <x-jet-label for="type" value="{{ __('Type') }}" />
                    <select
                        {{disableInput($master->status)?'disabled':''}}
                        name="type"
                        id="type"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model="type"
                        wire:model.debunce.800ms="type"
                        >
                        <option selected>-- Select --</option>
                        <option value="percentage">Percentage</option>
                        <option value="price">Price</option>

                    </select>
                    <x-jet-input-error for="type" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 mr-4">
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-label for="rate" value="{{ __('Rate') }}" />
                        <x-jet-input id="rate"
                            disabled="{{disableInput($master->status)}}"
                            type="text"
                            class="mt-1 block w-full"
                            wire:model="rate"
                            wire:model.defer="rate"
                            wire:model.debunce.800ms="rate" />
                        <x-jet-input-error for="rate" class="mt-2" />
                    </div>
                </div>

                @if($selected_agent && disableInput($master->status)!=1)
                    <div class="col-span-12 sm:col-span-1">
                        <a class="cursor-pointer mt-7 inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="removeAgent()">
                            Clear
                        </a>
                    </div>
                @elseif($commission && $commission->status!='draft')
                    <div class="col-span-12 sm:col-span-1 mr-4">
                        <div class="col-span-12 sm:col-span-1">
                            <x-jet-label for="total" value="{{ __('Total') }}" />
                            <x-jet-input id="total"
                                disabled="{{disableInput($master->status)}}"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="total"
                                wire:model.defer="total"
                                wire:model.debunce.800ms="total" />
                            <x-jet-input-error for="total" class="mt-2" />
                        </div>
                    </div>
                @endif
            </div>


        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Commission saved.') }}
            </x-jet-action-message>
            <x-jet-action-message class="mr-3" on="removed">
                {{ __('Agent removed.') }}
            </x-jet-action-message>

            <x-save-button show="{{$master->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />
</div>
