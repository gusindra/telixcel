<div>
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-sm col-span-3 h-screen max-h-full">
                <div class="bg-gray-400 h-14">
                    <div class="w-full mx-auto p-3 px-3">
                        <div class="flex items-center justify-between flex-wrap">
                            <div class="w-0 flex-1 flex items-center">
                                <x-jet-nav-link href="#active" >
                                    {{ __('Active') }}
                                </x-jet-nav-link>
                                <x-jet-nav-link href="#waiting" >
                                    {{ __('Waiting') }}
                                </x-jet-nav-link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-auto md:h-3/4 sm:h-1/2">
                    @foreach ($data as $item)
                        <a wire:click="chatCustomer('{{ $item->id }}')" class="cursor-pointer client-click">
                            <div class="text-sm whitespace-no-wrap">
                                <div class="md:flex px-2 py-2 {{$client_id!=0 && $client_id==$item->id?'bg-gray-300':'bg-white'}} hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 focus:ring-opacity-50">
                                    <div class="md:h-auto text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $item->profile_photo_url }}" alt="{{ $item->name }}" />
                                    </div>
                                    <div class="sm:max-w-sm sm:flex-none md:w-auto md:flex-auto flex-col items-start relative z-10 p-2 xl:p-2">
                                        <div class="flex">
                                            <div class="flex-grow">{{ $item->name }}</div>
                                            @if($item->active)
                                            <div class="flex-none flex-none w-auto text-center">
                                                <div class="px-1 rounded-full w-auto h-auto rounded-full bg-red-500 text-xs text-gray-50 text-center">1</div>
                                            </div>
                                            @endif
                                        </div>
                                        <p class="text-right text-xs">{{$item->date->diffForHumans()}}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <!-- <x-jet-nav-link href="#close">
                    {{ __('Closed') }}
                </x-jet-nav-link> -->
            </div>
            <div class="bg-gray-200 overflow-hidden shadow-xl sm:rounded-sm col-span-7 bg-blend-darken ">
                @livewire('chat-box', ['client_id' => $client_id], key($client_id))
            </div>
            @if($client)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-sm col-span-2">
                <div class="bg-gray-400 h-14">
                    <div class="w-full mx-auto p-3 px-3">
                        <div class="flex items-center justify-between flex-wrap">
                            <div class="w-0 flex-1 flex items-center">
                                <p class="ml-3 font-medium text-white truncate">
                                    <span class="hidden md:inline">
                                        Customer Info
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <input class="border-gray-300 focus:border-indigo-300 w-100 m-1 p-2 text-sm" type="text"  placeholder="{{ __('Customer Phone') }}"
                        x-ref="phone"
                        wire:model.defer="client_phone"
                        readonly disabled>
                    <input class="border-gray-300 focus:border-indigo-300 w-100 m-1 p-2 text-sm" type="text" placeholder="{{ __('Customer Name') }}"
                        x-ref="client_name"
                        wire:model.defer="client_name">
                    <textarea class="border-gray-300 focus:border-indigo-300 w-100 m-1 p-2 text-sm" id="note"
                        class="mt-1 block w-full mb-4"
                        placeholder="{{ __('note') }}"
                        wire:model.defer="note"
                        wire:model="note"></textarea>
                    <input class="border-gray-300 focus:border-indigo-300 w-100 m-1 p-2 text-sm" type="text" placeholder="{{ __('Tag') }}"
                        x-ref="tag"
                        wire:model.defer="tag">
                    <div class="px-3 text-right">
                        <button class="bg-gray-100 border border-gray-300 m-1 px-4 py-1" wire:click="save">
                            {{__('Save')}}
                        </button>
                        <x-jet-action-message class="mr-3" on="saved">
                            {{ __('Detail customer saved.') }}
                        </x-jet-action-message>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('chat-websocket')
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
                    let userId = "{{ auth()->user()->id }}";
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
                                from: '{{ $client_id }}',
                                user_id: '{{ auth()->user()->id }}'
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
                        messageBox.append(messageFormat(
                            chatMessage.user_id,
                            chatMessage.from,
                            chatMessage.message
                        ));
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
    @endpush
</div>
