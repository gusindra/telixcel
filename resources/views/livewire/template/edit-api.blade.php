<div>
    <x-jet-section-border />

    {{--Endpoint--}}
    <x-jet-action-section>
        <x-slot name="title">
            {{ __('Integration') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Check response from customer and sent to your aplication/server to get the data/anwser.') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-6 ">
                <div class="col-span-1 sm:col-span-1">
                    <div>
                        <x-jet-label for="request" value="{{ __('Request') }}" />
                    </div>
                    <select
                        name="request"
                        id="request"
                        class="border-gray-300 text-xs py-3 rounded-r-none dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="request"
                        >
                        <option value="get" selected>GET</option>
                        <option value="post">POST</option>
                        <option value="put">PUT</option>
                    </select>

                    <x-jet-input-error for="request" class="mt-2" />
                </div>
                <!-- Trigger Word -->
                <div class="col-span-5 sm:col-span-5">
                    <div>
                        <x-jet-label for="endpoint" value="{{ __('Endpoint') }}" />
                    </div>
                    <div >
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <!-- <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                http://
                            </span> -->
                            <input type="text" name="endpoint" id="endpoint" class="focus:ring-indigo-500 py-2 pb-3 dark:bg-slate-800 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300" placeholder="http://www.example.com"
                                wire:model="endpoint"
                                wire:model.defer="endpoint"
                                wire:model.debunce.800ms="endpoint"
                            >
                        </div>
                    </div>
                    <!-- <p class="text-sm text-gray-500">These is a word that will endpoint the action (`0`,`1`,`2`,`3`, `/help` etc).</p> -->

                    <x-jet-input-error for="endpoint" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-end text-right pt-3">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Endpoint saved.') }}
                </x-jet-action-message>

                @if($data)
                <x-jet-button wire:click="update" wire:loading.attr="disabled">
                    {{ __('Save') }}
                </x-jet-button>
                @else
                <x-jet-button wire:click="create" wire:loading.attr="disabled">
                    {{ __('Add') }}
                </x-jet-button>
                @endif
            </div>
        </x-slot>

    </x-jet-action-section>

    @if($data)
    @livewire('template.add-input', ['endpoint' => $data])
    @endif
</div>
