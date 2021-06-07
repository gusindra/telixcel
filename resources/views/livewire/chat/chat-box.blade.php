<div>
    <div class="bg-gray-400 h-14 lg:static md:static sm:fixed sm:inset-x-0 sm:top-0 shadow-md">
        <div class="w-full mx-auto p-3 px-3">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 flex items-center">
                @if($client)
                    <p class="ml-3 font-medium text-white truncate">
                        <div class="md:h-auto text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition pr-3">
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ $team->profile_photo_url }}" alt="{{ $team->name }}" />
                        </div>
                        <span class="hidden md:inline">
                            {{$team->name}}
                        </span>
                    </p>
                @endif
                </div>
                <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                    <button type="button" class="mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2"></button>
                </div>
            </div>
        </div>
    </div>
    <div id="messageArea" class="max-h-screen">
        <div id="messageBox" wire:poll class="overflow-auto h-96 bg-green-50 py-4" style="display: flex;flex-direction: column;">
            @foreach($data as $item)
            <div class="p-4 sm:p-4 sm:px-6 buble-chat object-left {{$item->source_id?'':'text-right right-0'}}">
                <small class="text-gray-500 font-medium">{{$item->source_id?$client->name:($item->from=='bot'?'Bot':$item->agent->name)}}</small>
                <div class="flex justify-between">
                    <div class="text-sm flex-auto z-10 p-2 xl:p-3 bg-gradient-to-br {{$item->source_id?'items-start':'order-last text-right'}} {{$item->source_id?'from-green-100 to-green-200 rounded-tr-lg rounded-b-lg':'from-indigo-100 to-indigo-200 rounded-b-lg rounded-tl-lg right-0'}}">
                        <span class="whitespace-pre-wrap">{{$item->reply}}</span><br>
                        <p class="px-3 text-xs font-thin text-gray-400 {{$item->source_id?'text-right right-0':'text-left left-0'}}">{{$item->date}}</p>
                    </div>
                    <div class="flex-1"></div>
                </div>
            </div>
            @endforeach

        </div>
        @if($client)
        <div class="bg-gray-200 py-1 grid grid-cols-6 lg:static md:static sm:fixed sm:inset-x-0 sm:bottom-0">
            <div class="flex items-center justify-center col-span-1 align-text-bottom">
                <button class="cursor-pointer text-sm text-grey-500 p-2" wire:click="actionShowModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                </button>
            </div>
            <div class="md:flex pl-2 py-2 col-span-4">
                <x-textarea
                    id="message"
                    class="mt-1 block w-full"
                    placeholder="{{ __('write a reply...') }}"
                    wire:model="message"
                />
            </div>
            <div class="flex items-center justify-center col-span-1 align-text-bottom">
                <button class="p-2 sm:p-2 md:p-4 lg:p-4 bg-green-600 text-white rounded-full" wire:click="sendMessage">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-90" viewBox="0 0 20 20" fill="currentColor">
                        <path style="transform: rotate(90deg);transform-origin: 50% 50%;" d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </div>
        </div>
        @endif
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

    <script>


            jQuery(document).ready(checkContainer);

            function checkContainer () {
                if($('#messageBox').is(':visible')){ //if the container is visible on the page
                    var d = $('#messageBox');
                    d.scrollTop(d.prop("scrollHeight"));
                } else {
                    alert(2);
                }
            }
    </script>
</div>
