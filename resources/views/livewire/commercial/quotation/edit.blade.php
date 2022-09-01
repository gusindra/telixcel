<div>
    <x-jet-form-section submit="update({{$quote->id}})">
        <x-slot name="title">
            {{ __('1. Quotation Basic') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The basic information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Title') }}" />
                    <x-jet-input id="name"
                        disabled="{{disableInput($quote->status)}}"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="name"
                                wire:model.defer="name"
                                wire:model.debunce.800ms="name" />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="quoteNo" value="{{ __('Quote No') }}" />
                    <div class="flex justify-around">
                        <x-jet-input id="quoteNo"
                            disabled="{{disableInput($quote->status)}}"
                                    type="text"
                                    class="mt-1 block w-full"
                                    wire:model="quoteNo"
                                    wire:model.defer="quoteNo"
                                    wire:model.debunce.800ms="quoteNo" />
                        @if(!disableInput($quote->status))
                        <a href="#" class="ml-4 mt-3 text-sm underline" wire:click="generateNo">Generate</a>
                        @endif
                    </div>
                    <x-jet-input-error for="quoteNo" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="date" value="{{ __('Quotation Date') }}" />
                    <x-input.date-picker disabled="{{disableInput($quote->status)}}" wire:model="date" :error="$errors->first('date')"/>
                    <x-jet-input-error for="date" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 md:mx-4">
                    <x-jet-label for="price" value="{{ __('Duration') }}" />
                    <select
                        {{disableInput($quote->status)?'disabled':''}}
                        name="valid_day"
                        id="valid_day"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="valid_day"
                        >
                        <option selected>-- Select --</option>
                        <option value="3">3 days</option>
                        <option value="7">7 days</option>
                        <option value="30">30 days</option>
                        <option value="60">60 days</option>
                    </select>
                    <x-jet-input-error for="price" class="mt-2" />
                </div>
            </div>
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="model" value="{{ __('Source') }}" />
                    <select
                        {{disableInput($quote->status)?'disabled':''}}
                        name="model"
                        id="model"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="model"
                        >
                        <option selected>-- Select --</option>
                        <option value="PROJECT">Project</option>
                        @if($source!="project")
                        <option value="COMPANY">Company</option>
                        <option value="CLIENT">Client</option>
                        @endif
                    </select>
                    <x-jet-input-error for="model" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 md:mx-4">
                    <x-jet-label for="model_id" value="{{ __('Source ID') }}" />
                    <select
                        {{disableInput($quote->status)?'disabled':''}}
                        name="model_id"
                        id="model_id"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="model_id"
                        >
                        <option selected>-- Select --</option>
                        @foreach($source_list as $key => $s)
                        <option value="{{$key}}">{{$s}}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="model_id" class="mt-2" />
                </div>
            </div>

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Quotation saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{$quote->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$quote->id}})">
        <x-slot name="title">
            {{ __('1. Customer Information') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Customer basic information.') }}
        </x-slot>

        <x-slot name="form">

            <div class="col-span-6 grid grid-cols-2">
                @if($source=="project" || $model="PROJECT")
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-label for="type" value="{{ __('Client Name') }}" />
                        <p class="border rounded-md shadow-sm mt-1 block w-full p-2">{{$quote->project->customer_name}}</p>
                    </div>

                    <div class="col-span-12 sm:col-span-1 md:mx-4">
                        <x-jet-label for="type" value="{{ __('Client Address') }}" />
                        <p class="border rounded-md shadow-sm mt-1 block w-full p-2">{{$quote->project->customer_address}}</p>
                    </div>
                @else
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-label for="addressed_company" value="{{ __('Customer') }}" />
                        @if($client)
                        <span class="border rounded-md shadow-sm mt-1 block w-full p-2">{{$client->name}}</span>
                        @else
                        <x-jet-input id="addressed_company"
                            disabled="{{disableInput($quote->status)}}"
                                    type="text"
                                    class="mt-1 block w-full"
                                    wire:model="addressed_company"
                                    wire:model.defer="addressed_company"
                                    wire:model.debunce.800ms="addressed_company" />
                        <x-jet-input-error for="addressed_company" class="mt-2" />
                        @endif
                    </div>

                    <div class="col-span-12 sm:col-span-1 md:mx-4">
                        <x-jet-label for="type" value="{{ __('Client') }}" />

                        <select
                            {{disableInput($quote->status)?'disabled':''}}
                            wire:change="onChangeModelId"
                            name="model_id"
                            id="model_id"
                            class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            wire:model.debunce.800ms="model_id"
                            >
                            <option selected>-- Select --</option>
                            @foreach($model_list as $key => $item)
                            <option value="{{$key}}">{{$item}}</option>
                            @endforeach

                            @if($quote->type!="project")
                            <option value="0">New Client</option>
                            @endif

                        </select>
                        <x-jet-input-error for="model_id" class="mt-2" />
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Quotation saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{$quote->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$quote->id}})">
        <x-slot name="title">2. Description</x-slot>

        <x-slot name="description">
            {{ __('The Product detail.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Description -->
            <div class="col-span-6 sm:col-span-6">
                <x-jet-label for="description" value="{{ __('Service Description') }}" />

                <x-textarea
                    disabled="{{disableInput($quote->status)}}"
                    wire:model="description"
                    wire:model.defer="description"
                    value="description" class="mt-1 block w-full h-32 text-sm"></x-textarea>
                <x-jet-input-error for="description" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Quotation saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{$quote->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    @livewire('commercial.quotation.item', ['data' => $quote])

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$quote->id}})">
        <x-slot name="title">4. Terms & Conditions :</x-slot>

        <x-slot name="description">
            {{ __('The Product detail.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Description -->
            <div class="col-span-6 sm:col-span-6">
                <x-textarea wire:model="terms"
                    disabled="{{disableInput($quote->status)}}"
                            wire:model.defer="terms"
                            value="terms" class="mt-1 block w-full h-32 text-sm" placeholder="ex: * Price exclude PPn 10%"></x-textarea>
                <x-jet-input-error for="terms" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Quotation saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{$quote->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$quote->id}})">
        <x-slot name="title">5. Footer :</x-slot>

        <x-slot name="description">
            {{ __('The Footer detail.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Description -->
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 md:mx-4">
                    <x-jet-label for="created_by" value="{{ __('Prepare By') }}" />
                    <x-jet-input id="created_by"
                        disabled="{{disableInput($quote->status)}}"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="created_by"
                                wire:model.defer="created_by"
                                wire:model.debunce.800ms="created_by" placeholder="Name" />
                    <x-jet-input id="created_role"
                        disabled="{{disableInput($quote->status)}}"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="created_role"
                                wire:model.defer="created_role"
                                wire:model.debunce.800ms="created_role" placeholder="Role" />
                    <x-jet-input-error for="created_role" class="mt-2" />
                </div>

                <div class="col-span-12 sm:col-span-1 md:mx-4">
                    <x-jet-label for="addressed_name" value="{{ __('Addressed To') }}" />
                    <x-jet-input id="addressed_name"
                        disabled="{{disableInput($quote->status)}}"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="addressed_name"
                                wire:model.defer="addressed_name"
                                wire:model.debunce.800ms="addressed_name" placeholder="Customer Name" />
                    <x-jet-input-error for="addressed_name" class="mt-2" />
                    <x-jet-input id="addressed_role"
                        disabled="{{disableInput($quote->status)}}"
                                type="text"
                                class="mt-1 block w-full placeholder-gray-500"
                                wire:model="addressed_role"
                                wire:model.defer="addressed_role"
                                wire:model.debunce.800ms="addressed_role" placeholder="Role" />
                    <x-jet-input-error for="addressed_role" class="mt-2" />
                    <x-jet-input id="addressed_company"
                        disabled="{{disableInput($quote->status)}}"
                                type="text"
                                class="mt-1 block w-full placeholder-gray-500"
                                wire:model="addressed_company"
                                wire:model.defer="addressed_company"
                                wire:model.debunce.800ms="addressed_company" placeholder="Company" />
                    <x-jet-input-error for="addressed_company" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Quotation saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{$quote->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>
</div>
