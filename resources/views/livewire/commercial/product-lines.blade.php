<div>
<x-jet-form-section submit="update({{$master->id}})">
        <x-slot name="title">
            {{ __('Product Lines :') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Product source details.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="product_line" value="{{ __('Product Lines') }}" />
                @if($selected_line=='')
                    <x-jet-input id="product_line"
                            disabled="{{$disabled}}"
                            type="text"
                            class="mt-1 block w-full"
                            wire:model="product_line"
                            wire:model.defer="product_line"
                            wire:model.debunce.800ms="product_line" />
                @else
                    <span class="border rounded-md shadow-sm mt-1 block w-full p-2 dark:bg-slate-800">{{$selected_line}}</span>
                @endif
                <x-jet-input-error for="product_line" class="mt-2" />
                @if(count($data['quick'])>0 || $product_line!="")
                <div :class="[open ? 'block' : 'block']" class="block">
                    <div class="z-40 left-0 mt-2 w-full">
                        <div class="py-1 text-sm rounded shadow-lg border border-gray-300">
                            @if(count($data['quick'])==0 && $product_line!="")
                                <a wire:click="addProduct()" class="block py-1 px-5 cursor-pointer hover:bg-indigo-600 hover:text-white">Add
                                        Product Line: "<span class="font-semibold" x-text="textInput">{{$product_line}}</span>"</a>
                            @else
                                @foreach ($data['quick'] as $quick)
                                    <a wire:click="selectProduct({{$quick->id}})" class="block py-1 px-5 cursor-pointer hover:bg-indigo-600 hover:text-white">Select
                                        Product Line: "<span class="font-semibold" x-text="textInput">{{$quick->name}}</span>"</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @if($selected_line!='' && !$disabled)
                <div class="col-span-2 sm:col-span-2">
                    <button type="submit" class="mt-7 inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="changeProduct()">
                        Change
                    </button>
                </div>
            @endif

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Quotation saved.') }}
            </x-jet-action-message>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

</div>
