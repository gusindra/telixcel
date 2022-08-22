<div>
    @if($commission->status =='unpaid' || $commission->status =='released')
    <x-jet-form-section submit="updateStatus({{$commission->id}})">
        <x-slot name="title">
            {{ __('Update') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Status information.') }}
        </x-slot>

        <x-slot name="form">

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="status" value="{{ __('Status') }}" />
                    <select
                        name="valid_day"
                        id="valid_day"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="input.status"
                        >
                        <option selected>-- Select --</option>
                        <option value="unpaid">Unpaid</option>
                        @if($commission->status =='released')
                        <option value="paid">Paid</option>
                        @endif
                        <!-- <option value="done">Done</option> -->
                    </select>
                    <x-jet-input-error for="status" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="update_status">
                {{ __('Status updated') }}
            </x-jet-action-message>

            <x-jet-button class="ml-2" type="submit" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>
    <x-jet-section-border />
    @endif


    <x-jet-form-section submit="#">
        <x-slot name="title">
            {{ __('Order Information') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Order & Customer basic information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <div class="col-span-6 sm:col-span-4">
                        <div class="col-span-12 sm:col-span-1">
                            <x-jet-label for="name" value="{{ __('Title') }}" />
                            <x-jet-input id="name"
                                disabled="{{disableInput($order->status)}}"
                                        type="text"
                                        class="mt-1 block w-full"
                                        wire:model="input.name"
                                        wire:model.defer="input.name"
                                        wire:model.debunce.800ms="input.name" />
                            <x-jet-input-error for="name" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="col-span-12 sm:col-span-1">
                    <div class="col-span-6 sm:col-span-4">
                        <div class="col-span-12 sm:col-span-1">
                            <x-jet-label for="no" value="{{ __('Order No') }}" />
                            <x-jet-input id="no"
                                disabled="{{disableInput($order->status)}}"
                                        type="text"
                                        class="mt-1 block w-full"
                                        wire:model="input.no"
                                        wire:model.defer="input.no"
                                        wire:model.debunce.800ms="input.no" />
                            <x-jet-input-error for="no" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <div class="col-span-6 sm:col-span-4">
                        <div class="col-span-12 sm:col-span-1">
                            <x-jet-label for="date" value="{{ __('Date') }}" />
                            <x-input.date-picker wire:model="input.date" show="{{disableInput($order->status)?true:false}}" :error="$errors->first('input.date')"/>
                            <x-jet-input-error for="date" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="addressed_company" value="{{ __('Customer') }}" />

                    @if($client)
                        @if($client->user && $client->user_id == 0)
                            <div class="absolute p-3 ml-20" style=" margin-top: -35px; ">
                                <a href="{{route('user.show', $client->user->id)}}" class="text-xs">view</a>
                            </div>
                        @endif
                        <span class="border dark:bg-slate-800 rounded-md shadow-sm mt-1 block w-full p-2 capitalize">{{$client->name}}</span>
                    @endif
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    @livewire('order.item', ['data' => $order])

    <x-jet-section-border />

    @livewire('commission.edit', ['model' => 'order', 'data' => $order])

</div>
