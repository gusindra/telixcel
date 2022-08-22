<div>
    @livewire('commercial.product-lines', ['model' => 'product', 'data' => $item])

    <x-jet-form-section submit="update({{$item->id}})">
        <x-slot name="title">
            {{ __('Basic') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Product basic information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="sku" value="{{ __('SKU') }}" />
                    <x-jet-input id="sku"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="sku"
                                wire:model.defer="sku"
                                wire:model.debunce.800ms="sku" />
                    <x-jet-input-error for="sku" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="type" value="{{ __('Status') }}" />
                    <select
                        name="status"
                        id="status"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="status"
                        >
                        <option selected>-- Select Status --</option>
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="compleated">Disabled</option>
                    </select>
                    <x-jet-input-error for="status" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="type" value="{{ __('Type') }}" />
                <select
                    name="type"
                    id="type"
                    class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="type"
                    >
                    <option selected>-- Select Party --</option>
                    <option value="sku">SKU</option>
                    <option value="nosku">Without SKU</option>
                    <option value="one_time">One Time Service</option>
                    <option value="monthly">Monthly Service</option>
                    <option value="anually">Yearly Serivce</option>
                </select>
                <x-jet-input-error for="type" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="import" value="{{ __('Import') }}" />
                <select
                    name="import"
                    id="import"
                    class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="import"
                    >
                    <option selected>-- Select Import Way --</option>
                    <option value="none">None</option>
                    <option value="fob">FOB (Free on Board)</option>
                    <option value="ddp">DDP (Delivered Duty Paid)</option>
                </select>
                <x-jet-input-error for="import" class="mt-2" />
            </div>

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Product saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$item->id}})">
        <x-slot name="title">Description</x-slot>

        <x-slot name="description">
            {{ __('The Product detail.') }}
        </x-slot>

        <x-slot name="form">

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Product Name') }}" />
                    <x-jet-input id="name"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="name"
                                wire:model.defer="name"
                                wire:model.debunce.800ms="name" />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
            </div>
            <!-- Description -->
            <div class="col-span-6 sm:col-span-6">
                <x-jet-label for="description" value="{{ __('Description') }}" />

                <x-textarea wire:model="description"
                            wire:model.defer="description"
                            value="description" class="mt-1 block w-full"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <!-- Specification -->
            <div class="col-span-6 sm:col-span-6">
                <x-jet-label for="spec" value="{{ __('Specification') }}" />

                <x-textarea wire:model="spec"
                            wire:model.defer="spec"
                            value="spec" class="mt-1 block w-full"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Product saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$item->id}})">
        <x-slot name="title">Price</x-slot>

        <x-slot name="description">
            {{ __('Unit price / listing price.') }}
        </x-slot>

        <x-slot name="form">

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="price" value="{{ __('Price') }}" />
                    <x-jet-input id="price"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="price"
                                wire:model.defer="price"
                                wire:model.debunce.800ms="price" />
                    <x-jet-input-error for="price" class="mt-2" />
                </div>
                @if($item->type == "nosku")
                <div class="col-span-10 sm:col-span-1 mx-4">
                    <x-jet-label for="unit" value="{{ __('Unit') }}" />
                    <x-jet-input id="unit"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="unit"
                                wire:model.defer="unit"
                                wire:model.debunce.800ms="unit" />
                    <x-jet-input-error for="unit" class="mt-2" />
                </div>
                @endif
            </div>

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="discount" value="{{ __('General Discount') }}" />
                    <x-jet-input id="discount"
                                type="text"
                                class="mt-1 block w-full"
                                wire:model="discount"
                                wire:model.defer="discount"
                                wire:model.debunce.800ms="discount" />
                    <x-jet-input-error for="discount" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Product saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <div class="md:grid md:grid-cols-5 md:gap-6">
        <div class="md:col-span-1 flex justify-between">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-gray-900 dark:text-slate-300">Pics</h3>

                <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">
                    The Product picture.
                </p>
            </div>

            <div class="px-4 sm:px-0"> </div>
        </div>

        <div class="mt-0 md:mt-0 md:col-span-4">
            <div class=" bg-white dark:bg-slate-600 shadow sm:rounded-md">
                <div class="">
                    @livewire('image-upload', ['model'=> 'product', 'model_id'=>$item->id])
                </div>
            </div>
        </div>
    </div>

    <x-jet-section-border />

    @livewire('commission.edit', ['model' => 'product', 'data' => $item])

</div>
