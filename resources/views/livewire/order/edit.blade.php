<div>
    @if($order->attachments)
        @foreach ($order->attachments as $file)
            <div class="bg-blue-100 border sm:rounded border-blue-500 text-blue-700 px-4 py-3 mb-4" role="alert">
                <div class="flex justify-between">
                    <span class="text-sm">Attachment:</span> <a wire:click="actionShowModal('https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$file->file}}')" href="#">Lihat</a>
                </div>
            </div>
        @endforeach

        <!-- Modal Detail -->
        <x-jet-dialog-modal wire:model="modalAttach">
            <x-slot name="title">
                <div class="text-center font-bold text-2xl">{{ __('Detail Pembayaran') }}</div>
            </x-slot>

            <x-slot name="content">
                <div class="p-4">
                    <div class="flex justify-between py-2">
                        <img src="{{ $url }}" />
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('modalAttach')" wire:loading.attr="disabled">
                    {{ __('x') }}
                </x-jet-secondary-button>
            </x-slot>
        </x-jet-dialog-modal>
    @endif

    @if(($order->status=='draft' || $order->status=='unpaid' || $order->status=='paid') && @Auth::user()->role && Auth::user()->super->first() && @Auth::user()->super->first()->role == 'superadmin')
        <x-jet-form-section submit="updateStatus({{$order->id}})">
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
                            @if($order->status=='draft')
                            <option value="draft">Draft</option>
                            @endif
                            @if($order->status=='unpaid' || $order->status=='paid')
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="process">Process</option>
                            <option value="refund">Refund</option>
                            @endif
                            @if($order->status=='paid')
                            <option value="done">Done</option>
                            @endif
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

    <x-jet-form-section submit="update({{$order->id}})">
        <x-slot name="title">
            {{ __('1. Order Information') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The basic information.') }}
        </x-slot>

        <x-slot name="form">
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

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="no" value="{{ __('Order No') }}" />
                    <div class="flex justify-between">
                        <x-jet-input id="no"
                            disabled="{{disableInput($order->status)}}"
                                    type="text"
                                    class="mt-1 block w-full"
                                    wire:model="input.no"
                                    wire:model.defer="input.no"
                                    wire:model.debunce.800ms="input.no" />
                        @if(!disableInput($order->status))
                            <a href="#" class="ml-4 mt-3 text-sm underline" wire:click="generateNo">Generate</a>
                        @endif
                    </div>
                    <x-jet-input-error for="no" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="date" value="{{ __('Date') }}" />
                    <x-input.date-picker show="{{disableInput($order->status)?true:false}}" wire:model="input.date" :error="$errors->first('input.date')"/>
                    <x-jet-input-error for="date" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Order basic saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{$order->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$order->id}})">
        <x-slot name="title">
            {{ __('2. Customer Information') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Customer basic information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="input.customer_id" value="{{ __('Client') }}" />
                    <select
                        {{disableInput($order->status)?'disabled':''}}
                        wire:change="onChangeModelId"
                        name="input.customer_id"
                        id="input.customer_id"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="input.customer_id"
                        >
                        <option selected>-- Select --</option>
                        @foreach($model_list as $key => $item)
                        <option value="{{$key}}">{{$item}}</option>
                        @endforeach
                        <option value="new">New User</option>

                    </select>
                    <x-jet-input-error for="input.customer_id" class="mt-2" />
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
                    @if($input['customer_id']=='new')
                        <div class="py-3">
                            @livewire('user.add', ['model'=>'order'])
                        </div>
                    @endif
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Order saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{$order->status=='draft'?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    @livewire('order.item', ['data' => $order])

    <x-jet-section-border />

    @livewire('commission.edit', ['model' => 'order', 'data' => $order])

</div>
