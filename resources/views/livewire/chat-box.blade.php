<div>
    <div class="bg-gray-300 ">
        <div class="w-full mx-auto p-3 px-3">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 flex items-center">
                    <p class="ml-3 font-medium text-white truncate">
                        <div class="md:h-auto text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition pr-3">
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ $client->profile_photo_url }}" alt="{{ $client->name }}" />
                        </div>
                        <span class="hidden md:inline">
                            {{$client->name}}
                        </span>
                    </p>
                </div>
                <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                    <button type="button" class="mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2"></button>
                </div>
            </div>
        </div>
    </div>
    <div class="">
        <div id="messageBox" wire:poll class="overflow-auto h-80 bg-green-50 py-4" style="display: flex;flex-direction: column;">
            @foreach($data as $item)
            <div class="px-12 buble-chat object-left {{$item->from==$item->client_id?'':'text-right'}}">
                <small class="ml-2">{{$item->from==$item->client_id?$item->client->name:$item->agent->name}}</small>
                <div class="text-right mb-2 flex md:flex-col bg-gradient-to-br {{$item->from==$item->client_id?'from-green-100 to-green-200 rounded-tr-2xl rounded-b-xl':'from-indigo-100 to-indigo-200 rounded-bl-2xl rounded-t-xl'}}">
                    <div class="sm:flex-none md:flex-auto flex flex-col {{$item->from==$item->client_id?'items-start':''}} relative z-10 p-3 xl:p-3">
                        {{$item->reply}}
                    </div>
                </div>
            </div>
            @endforeach

        </div>
        <div class="md:flex px-4 py-2">
            <button class="cursor-pointer text-sm text-grey-500 p-2" wire:click="actionShowModal">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </button>
            <x-textarea
                id="message"
                class="mt-1 block w-full"
                placeholder="{{ __('write a reply...') }}"
                wire:model="message"
            />
        </div>
        <div class="flex items-center justify-end px-4">
            <x-jet-button wire:click="sendMessage">
                {{__('Send')}}
            </x-jet-button>
        </div>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalAttachment">
        <x-slot name="title">
            {{ __('Attachment message') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                    <!-- Profile Photo File Input -->
                    <input type="file" class="hidden"
                                wire:model="photo"
                                x-ref="photo"
                                x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                " />

                    <x-jet-label for="photo" value="{{ __('Photo') }}" />

                    <!-- New Profile Photo Preview -->
                    <div class="mt-2" x-show="photoPreview">
                        <span class="block h-48 w-full"
                            x-bind:style="'background-size: contain; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <x-jet-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                        {{ __('Select A New Photo') }}
                    </x-jet-secondary-button>

                    <x-jet-input-error for="photo" class="mt-2" />
                </div>
                <x-textarea wire:model="message"
                            wire:model.defer="message"
                            value="message" class="mt-1 block w-full" placeholder="Caption"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalAttachment')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="sendAttachment" wire:loading.attr="disabled">
                {{ __('Send') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
