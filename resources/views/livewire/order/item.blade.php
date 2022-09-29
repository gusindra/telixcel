<div>
    <x-jet-action-section>
        <x-slot name="title">Order items :</x-slot>

        <x-slot name="description">
            {{ __('The Order Product detail.') }}
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
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8" wire:poll.visible>
                            @if ($data['items']->count()>0)
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 mt-2">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Item</th>
                                                <th class="px-3 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-11">Price</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Qty</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2"></th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Amount</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 dark:text-slate-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"> </th>
                                            </tr>
                                        </thead>
                                        <tbody wire:poll.visible class="bg-white dark:bg-slate-600 divide-y divide-gray-200">
                                            @foreach ($data['items'] as $item)
                                                <tr>
                                                    <td class="px-3 text-xs py-4 whitespace-no-wrap w-full">
                                                        <b>{{ $item->name }}</b><br>{{ $item->note }}
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-no-wrap">
                                                        {{ number_format($item->price) }}
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-no-wrap w-100">
                                                        {{ $item->qty }} {{$item->unit }}
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-no-wrap w-auto">
                                                        {{$item->total_percentage!=100?$item->total_percentage.'%':''}}
                                                    </td>
                                                    <td class="px-6 py-4 text-xs whitespace-no-wrap w-100 text-right">
                                                        {{$item->total_percentage!=100 ? number_format(($item->total_percentage * $item->qty * $item->price) /100) : number_format($item->qty * $item->price) }}
                                                    </td>
                                                    <td class="items-center justify-end py-3 text-right {{$data->status=='draft'?'':'hidden'}}">
                                                        <div class="items-center flex">
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
                                <div class="mt-10">
                                    <div class="col-span-6 grid grid-cols-2 mt-4"  >
                                        <div class="col-span-12 sm:col-span-1 mx-4">
                                            <x-jet-label for="input" value="{{ __(' ') }}" />
                                            <x-jet-input-error for="input" class="mt-2" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-1 mx-4">
                                            <x-jet-label for="input.customer_id" value="{{ __('Sub Total') }}" />
                                            <span class="border dark:bg-slate-700 rounded-md shadow-sm mt-1 block w-full p-2 text-right">Rp {{number_format($data['total'])}}</span>
                                            <x-jet-input-error for="input.total" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-span-6 grid grid-cols-4 mt-4" >
                                        <div class="col-span-4 sm:col-span-1 mx-4">
                                            <x-jet-label for="tax" value="{{ __('VAT (%)') }}" />
                                            <x-jet-input id="name"
                                                wire:change="updateTax"
                                                disabled="{{disableInput($data->status)}}"
                                                        type="text"
                                                        class="mt-1 block w-full text-right"
                                                        wire:model="tax"
                                                        wire:model.defer="tax"
                                                        wire:model.debunce.800ms="tax" />
                                            <x-jet-input-error for="tax" class="mt-2" />
                                        </div>
                                        <div class="col-span-4 sm:col-span-1 mx-4 mt-6">
                                        </div>
                                        <div class="col-span-12 sm:col-span-2 mx-4">
                                            <x-jet-label for="tax" value="{{ __('VAT') }}" />
                                            <span class="border dark:bg-slate-700 rounded-md shadow-sm mt-1 block w-full p-2 text-right">Rp {{number_format($data['total']*$tax/100)}}</span>
                                            <x-jet-input-error for="tax" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-span-6 grid grid-cols-2 mt-4" >

                                        <div class="col-span-12 sm:col-span-1 mx-4">
                                            <x-jet-label for="input" value="{{ __(' ') }}" />
                                            <x-jet-input-error for="input" class="mt-2" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-1 mx-4">
                                            <x-jet-label for="input.customer_id" value="{{ __('Total') }}" />
                                            <span class="border dark:bg-slate-700 rounded-md shadow-sm mt-1 block w-full p-2 text-right">Rp {{number_format($data['total'] + ($data['total']*$tax/100))}}</span>
                                            <x-jet-input-error for="input.total" class="mt-2" />
                                        </div>
                                    </div>
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
            {{ __('Add Item') }}
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
                <div class="ml-3">
                    <x-jet-label for="qty" value="{{ __('Quantity') }}" />
                    <x-jet-input id="qty" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="qty" autofocus />
                    <x-jet-input-error for="qty" class="mt-2" />
                </div>
                <div class="ml-1">
                    <x-jet-label for="unit" value="{{ __('Unit Measurement') }}" />
                    <x-jet-input id="unit" placeholder="meter, unit, item, dll" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="unit" autofocus />
                    <x-jet-input-error for="unit" class="mt-2" />
                </div>
            </div>
            <div class="col-span-6 sm:col-span-1 p-3">
                <x-jet-label for="name" value="{{ __('Percentage of Grand Total') }}" />
                <div class="flex">
                    <x-jet-input id="name" type="text" class="mt-1 block w-full text-right" wire:model.debunce.800ms="percentage" autofocus />
                    <span class="p-3 bordered border-1">%</span>
                </div>
                <span class="text-xs">Default 100% of total. You can set for commission or percentage after discount.</span>
                <x-jet-input-error for="name" class="mt-2" />
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
                <x-jet-secondary-button class="dark:bg-gray-600" wire:click="modalProductVisible">
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
                    <div class="col-span-6 sm:col-span-4 p-3">
                        <x-jet-label for="unit" value="{{ __('Unit Measurement') }}" />
                        <x-jet-input id="unit" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="unit" autofocus />
                        <x-jet-input-error for="unit" class="mt-2" />
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
