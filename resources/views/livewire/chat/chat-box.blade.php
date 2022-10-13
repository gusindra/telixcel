<div>
    <div class="bg-gray-400 dark:bg-slate-700 sm:h-12 lg:h-14 lg:static md:static sm:fixed sm:inset-x-0 sm:top-0 shadow-md">
        <div class="w-full mx-auto p-3 px-3">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 flex items-center">
                @if($team && $client)
                    <p class="ml-3 font-medium text-white truncate">
                        <div class="md:h-auto text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition pr-3">
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ $team->profile_photo_url }}" alt="{{ $team->name }}" />
                        </div>
                        <span class="md:inline text-white">
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
    <div id="messageArea" class="lg:max-h-screen">
        <div id="messageBox" wire:poll class="overflow-auto h-96 bg-green-50 dark:bg-slate-600 py-4" style="display: flex;flex-direction: column;height: 80vh;overflow: auto;">
            @foreach($data as $item)
            <div class="pb-1 px-4 sm:p-2 sm:px-3 buble-chat object-left {{$item->source_id?'':'text-right right-0'}}">
                <small class="text-gray-500 dark:text-slate-300 font-medium">{{$item->source_id?$client->name:($item->from=='bot' || $item->from=='api'?'Bot':$item->agent->name)}}</small>
                <div class="flex justify-between">
                    <div class="text-sm flex-auto z-10 p-2 xl:p-3 bg-gradient-to-br {{$item->source_id?'items-start':'order-last text-right'}} {{$item->source_id?'from-green-100 to-green-200 rounded-tr-lg rounded-b-lg':'from-indigo-100 to-indigo-200 dark:bg-slate-700 rounded-b-lg rounded-tl-lg right-0'}}">
                        @if($item->type=='image')
                            <img src="{{$item->media}}" class="bg-gray-100 max-w-400 min-w-1/2 right-0 order-last" />
                        @elseif($item->type=='document')
                            <div class="flex justify-between">
                                <div><img src="{{url('/backend/img/'.substr(strrchr($item->media, '.'), 1).'.png')}}" class="h-8" /></div>
                                <div class="text-left mt-1">{{substr(strrchr($item->media, '/'), 1)}}</div>
                                <div><a href="{{$item->media}}" data-testid="audio-download" data-icon="audio-download" class=""><svg viewBox="0 0 34 34" width="34" height="34" class=""><path fill="currentColor" d="M17 2c8.3 0 15 6.7 15 15s-6.7 15-15 15S2 25.3 2 17 8.7 2 17 2m0-1C8.2 1 1 8.2 1 17s7.2 16 16 16 16-7.2 16-16S25.8 1 17 1z"></path><path fill="currentColor" d="M22.4 17.5h-3.2v-6.8c0-.4-.3-.7-.7-.7h-3.2c-.4 0-.7.3-.7.7v6.8h-3.2c-.6 0-.8.4-.4.8l5 5.3c.5.7 1 .5 1.5 0l5-5.3c.7-.5.5-.8-.1-.8z"></path></svg></a></div>
                            </div>
                        @elseif($item->type=='video')
                            <video width="320" height="240" controls>
                                <source src="{{$item->media}}" type="video/mp4">
                                <source src="{{$item->media}}" type="video/ogg">
                                Your browser does not support the video tag.
                            </video>
                        @elseif($item->type=='audio')
                            <audio controls>
                                <source src="{{$item->media}}" type="audio/ogg">
                                <source src="{{$item->media}}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        @else
                            <span class="whitespace-pre-wrap dark:text-slate-600 text-base">{{$item->reply}}</span><br>
                        @endif
                        <p class="px-1 text-xs font-thin text-gray-400 dark:text-slate-500 {{$item->source_id?'text-right right-0':'text-left left-0'}}">{{$item->date}}</p>
                    </div>
                    <div class="flex-1"></div>
                </div>
            </div>
            @endforeach

        </div>
        @if($client)
        <div class="bg-gray-200 dark:bg-slate-700 py-1 grid grid-cols-6 lg:static md:static sm:fixed sm:inset-x-0 sm:bottom-0">
            <div class="flex items-center justify-center col-span-1 align-text-bottom">
                <button class="cursor-pointer text-sm text-grey-500 dark:text-slate-300 p-2" wire:click="actionShowModal">
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-30" viewBox="0 0 20 20" fill="currentColor">
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
        setTimeout(function (){
            if($('#messageBox').is(':visible')){ //if the container is visible on the page
                var d = $('#messageBox');
                d.scrollTop(d.prop("scrollHeight"));
            } else {
                alert(2);
            }
        }, 100)
    </script>

    <script>
        $(function(){
            /**
             * Keeps the chat message box focus
             * at the bottom.
             *
             * @param {string} elementId
             */
            function keepChatboxFocusAtBottom(elementId) {
                var element = document.getElementById(elementId);
                element.scrollTop = element.scrollHeight;
            }
            /**
             * Returns the chat message proper format
             *
             * @param {string} id
             * @param {string} username
             * @param {string} message
             */
            function messageFormat(id, name, message) {
                let userId = "{{ $client->user_id }}";
                let color = id == userId ? "bg-blue-400" : "bg-green-400";
                let alignment = id == userId ? "text-right" : "text-left";
                return `
                    <div class="grid grid-cols-1 items-center gap-0">
                        <span class="${alignment} font-semibold text-sm">${name}</span>
                        <span class="${alignment} ${color} text-sm text-white px-3 py-2 rounded mb-2">${message}</span>
                    </div>
                `;
            }

            // Instantiate a connection
            var chatConnection = clientSocket({ port: 3281 });
            // The messageBox element
            var messageBox = $("#messageBox");
            // The message element
            var message = $("#message");

            /**
             * When the connection is open
             */
            chatConnection.onopen = function () {
                // console.log("Chat connection is open!");
                // Send the information of the client user here
                chatConnection.send(
                    JSON.stringify({
                        type: "info",
                        data: {
                            from: '{{ $client->id }}',
                            user_id: '{{ $client->user_id }}'
                        }
                    })
                );
            }

            /**
             * Will receive messages from the websocket server
             */
            chatConnection.onmessage = function(message){
                var result = JSON.parse(message.data);
                console.log(result);
                var chatMessage = result.data;
                if (result.type == "chatMessage") {
                    // messageBox.append(messageFormat(
                    //     chatMessage.user_id,
                    //     chatMessage.from,
                    //     chatMessage.message
                    // ));
                }
                keepChatboxFocusAtBottom("messageBox");
            }

            /**
             * Send the prompt to the websocket server
             */
            window.addEventListener('chat-send-message', event => {
                console.log(event.detail);
                chatConnection.send(JSON.stringify({
                    type: "chatMessage",
                    date: event.detail
                }));
            });

            /**
             * Reload the page
             */
            window.addEventListener('reload-page', event => {
            //    window.location.reload();
            });
        });
    </script>
    <script>
        // Instaniate a connection
        var connection = clientSocket();

        /**
         * The event listener that will be dispatched
         * to the websocket server.
         */

        window.addEventListener('event-notification', event => {
            // alert('Event '+ event.detail.eventName);
            connection.send(JSON.stringify({
                eventName: event.detail.eventName,
                eventMessage: event.detail.eventMessage
            }));
        })

        /**
         * When the connection is open
         */
        connection.onopen = function (){
            console.log("Connection is open");
        }

        /**
         * When the connection is open
         */
        connection.onclose = function (){
            console.log("Connection was closed!");
            console.log("Reconnecting after 60 seconds...");
            setTimeout(() => {
                // window.location.reload();
            }, 5000)
        }

        connection.onmessage = function (message){
            var result = JSON.parse(message.data);
            console.log(result);

            var notificationMessage = `
                <div class="sm:max-w-sm sm:flex-none md:w-auto md:flex-auto flex flex-col items-start relative z-10 p-3 xl:p-3">${result.eventMessage}</div>
            `;

            document.getElementById('chat-event').innerHTML = notificationMessage;
        }
    </script>
</div>
