<div>
    <x-jet-action-section>
        <x-slot name="title">3. Offering Price :</x-slot>

        <x-slot name="description">
            {{ __('The Product detail.') }}
        </x-slot>

        <!-- Team Action List -->
        <x-slot name="content">
            <div class="flex items-center justify-end text-right">
                <x-jet-action-message class="mr-3" on="added">
                    {{ __('Action added.') }}
                </x-jet-action-message>
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Action saved.') }}
                </x-jet-action-message>
                <x-save-button show="{{$data->status=='draft'?true:false}}" wire:click="showCreateModal">
                    {{__('Add Item')}}
                </x-save-button>
            </div>

            <div class="space-y-6">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            @if ($items->count())
                            <div class="mt-2 shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Item</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-11">Price</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Unit Measurement</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Desc</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4 {{$data->status=='draft'?'':'hidden'}}"> </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-slate-700 divide-y divide-gray-200">
                                        @foreach ($items as $item)
                                            <tr>
                                                <td class="px-6 text-xs py-4 whitespace-no-wrap">
                                                    {{ $item->name }}
                                                </td>
                                                <td class="px-6 py-4 text-xs whitespace-no-wrap">
                                                    {{ $item->price }}
                                                </td>
                                                <td class="px-6 py-4 text-xs whitespace-no-wrap">
                                                    {{ $item->qty }} {{ $item->unit }}
                                                </td>
                                                <td class="px-6 py-4 text-xs whitespace-no-wrap w-100">
                                                    {{ $item->note }}
                                                </td>
                                                <td class="items-center justify-end py-3 text-right {{$data->status=='draft'?'':'hidden'}}">
                                                    <div class="items-center flex">
                                                        <button title="{{ __('Edit') }}" class="cursor-pointer m-2 w-auto text-xs p-1 bg-yellow-200 text-gray-800 rounded" wire:click="updateShowModal('{{ $item->id }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <button title="{{ __('Delete') }}" class="cursor-pointer m-2 w-auto text-xs p-1 bg-red-200 text-gray-800 rounded" wire:click="deleteShowModal('{{ $item->id }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </x-slot>
    </x-jet-action-section>

    <!-- Form Input Modal -->
    <x-jet-dialog-modal wire:model="modalVisible">
        <x-slot name="title">
            {{ __('Add Product') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="name" value="{{ __('Item Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 p-3 grid grid-cols-3">
                <div class=" ">
                    <x-jet-label for="price" value="{{ __('Price') }}" />
                    <x-jet-input id="price" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="price" autofocus />
                    <x-jet-input-error for="price" class="mt-2" />
                </div>
                <div class="mx-3">
                    <x-jet-label for="qty" value="{{ __('Qty') }}" />
                    <x-jet-input id="qty" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="qty" autofocus />
                    <x-jet-input-error for="qty" class="mt-2" />
                </div>
                <div class="mx-3">
                    <x-jet-label for="unit" value="{{ __('Unit Measurement') }}" />
                    <x-jet-input id="unit" placeholder="item / meter / kg / etc" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="unit" autofocus />
                    <x-jet-input-error for="unit" class="mt-2" />
                </div>
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="description" value="{{ __('Description') }}" />
                <x-jet-input id="description" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="description" autofocus />
                <x-jet-input-error for="description" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            @if($item_id)
                <x-jet-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
                    {{ __('Save') }}
                </x-jet-button>
            @else
                <x-jet-secondary-button wire:click="modalProductVisible">
                    {{__('Select Product')}}
                </x-jet-secondary-button>
                <x-jet-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
                    {{ __('Add') }}
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="modalProductVisible">
        <x-slot name="title">
            {{ __('Select Product') }}
        </x-slot>

        <x-slot name="content">
                @if($products)
                    <div class="col-span-6 sm:col-span-4 p-3">
                        <x-jet-label for="selectedTemplate" value="{{ __('Product') }}" />
                        <select
                            name="selectedProduct"
                            id="selectedProduct"
                            class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            wire:model.debunce.800ms="selectedProduct"
                            >
                            <option selected>-- Select Product --</option>
                            @foreach($products as $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for="selectedProduct" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4 p-3 grid grid-cols-2">
                        <div class="mx-3">
                            <x-jet-label for="qty" value="{{ __('Qty') }}" />
                            <x-jet-input id="qty" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="qty" autofocus />
                            <x-jet-input-error for="qty" class="mt-2" />
                        </div>
                        <div class="mx-3">
                            <x-jet-label for="unit" value="{{ __('Unit Measurement') }}" />
                            <x-jet-input id="unit" placeholder="item / meter / kg / etc" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="unit" autofocus />
                            <x-jet-input-error for="unit" class="mt-2" />
                        </div>
                    </div>
                    <div class="col-span-6 sm:col-span-4 p-3">
                        <x-jet-label for="description" value="{{ __('Description') }}" />
                        <x-jet-input id="description" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="description" autofocus />
                        <x-jet-input-error for="description" class="mt-2" />
                    </div>
                @else
                    <div>
                        No Product, you can try to add some product.
                    </div>
                @endif
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalProductVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            @if($item_id)
            <x-jet-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
            @else
            <x-jet-secondary-button wire:click="showCreateModal">
                {{__('Direct Add')}}
            </x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="addProduct" wire:loading.attr="disabled">
                {{ __('Add') }}
            </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Remove Input Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingModalRemoval">
        <x-slot name="title">
            {{ __('Remove Confirmation') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this message?') }}<br>
            <b>`{{$name}}`</b>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingModalRemoval')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
