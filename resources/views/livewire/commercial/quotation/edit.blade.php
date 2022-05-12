<div>
    <!-- <div class="text-right mb-4">
        <a target="_blank" href="{{route('commercial.quotation.print', ['key'=>'quotation','quotation'=>$quote->id])}}" class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs   uppercase tracking-widest hover:bg-gray-300 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition">Print</a>
    </div> -->
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
                    <x-jet-input id="quoteNo"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="quoteNo"
                                wire:model.defer="quoteNo"
                                wire:model.debunce.800ms="quoteNo" />
                    <x-jet-input-error for="quoteNo" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="date" value="{{ __('Quotation Date') }}" />
                    <x-input.date-picker wire:model="date" :error="$errors->first('date')"/>
                    <x-jet-input-error for="date" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="price" value="{{ __('Duration') }}" />
                    <select
                        name="valid_day"
                        id="valid_day"
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
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

    <x-jet-form-section submit="update({{$quote->id}})">
        <x-slot name="title">
            {{ __('1. Customer Information') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Customer basic information.') }}
        </x-slot>

        <x-slot name="form">

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="addressed_company" value="{{ __('Customer') }}" />
                    @if($client)
                    <span class="border rounded-md shadow-sm mt-1 block w-full p-2">{{$client->name}}</span>
                    @else
                    <x-jet-input id="addressed_company"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="addressed_company"
                                wire:model.defer="addressed_company"
                                wire:model.debunce.800ms="addressed_company" />
                    <x-jet-input-error for="addressed_company" class="mt-2" />
                    @endif
                </div>

                <div class="col-span-12 sm:col-span-1 mx-4">
                    @if($quote->type=="project")
                    <x-jet-label for="type" value="{{ __('Project') }}" />
                    @else
                    <x-jet-label for="type" value="{{ __('Client') }}" />
                    @endif
                    <select
                        wire:change="onChangeModelId"
                        name="model_id"
                        id="model_id"
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
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

    <x-jet-form-section submit="update({{$quote->id}})">
        <x-slot name="title">2. Description</x-slot>

        <x-slot name="description">
            {{ __('The Product detail.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Description -->
            <div class="col-span-6 sm:col-span-6">
                <x-jet-label for="description" value="{{ __('Service Description') }}" />

                <x-textarea wire:model="description"
                            wire:model.defer="description"
                            value="description" class="mt-1 block w-full h-32 text-sm"></x-textarea>
                <x-jet-input-error for="description" class="mt-2" />
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
                            wire:model.defer="terms"
                            value="terms" class="mt-1 block w-full h-32 text-sm" placeholder="ex: * Price exclude PPn 10%"></x-textarea>
                <x-jet-input-error for="terms" class="mt-2" />
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

    <x-jet-form-section submit="update({{$quote->id}})">
        <x-slot name="title">5. Footer :</x-slot>

        <x-slot name="description">
            {{ __('The Footer detail.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Description -->
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="created_by" value="{{ __('Prepare By') }}" />
                    <x-jet-input id="created_by"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="created_by"
                                wire:model.defer="created_by"
                                wire:model.debunce.800ms="created_by" placeholder="Name" />
                    <x-jet-input id="created_role"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="created_role"
                                wire:model.defer="created_role"
                                wire:model.debunce.800ms="created_role" placeholder="Role" />
                    <x-jet-input-error for="created_role" class="mt-2" />
                </div>

                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="addressed_name" value="{{ __('Addressed To') }}" />
                    <x-jet-input id="addressed_name"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="addressed_name"
                                wire:model.defer="addressed_name"
                                wire:model.debunce.800ms="addressed_name" placeholder="Customer Name" />
                    <x-jet-input id="addressed_role"
                                type="text"
                                class="mt-1 block w-full placeholder-gray-500"
                                wire:model="addressed_role"
                                wire:model.defer="addressed_role"
                                wire:model.debunce.800ms="addressed_role" placeholder="Role" />
                    <x-jet-input-error for="addressed_role" class="mt-2" />
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
</div>
