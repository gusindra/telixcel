<div>
    <div class="max-w-full mx-1 sm:px-1 lg:px-1">
        <div class="lg:grid lg:grid-cols-12 gap-2 lg:h-screen">
            <!-- List Customer -->
            <div class="bg-white dark:bg-slate-600 mt-2 overflow-hidden shadow-xl sm:rounded-sm col-span-3 lg:h-screen lg:max-h-full">
                <div class="bg-gray-400 dark:bg-slate-600  h-10">
                    <div class="w-full mx-auto">
                        <div class="flex items-center justify-between flex-wrap">
                            <div class="w-0 flex-1 flex items-center">
                                <!-- <x-jet-nav-link href="#active" >
                                    {{ __('Active') }}
                                </x-jet-nav-link>
                                <x-jet-nav-link href="#waiting" >
                                    {{ __('Waiting') }}
                                </x-jet-nav-link> -->
                                <div class="form-check mx-2 mt-2">
                                    <label class="form-check-label ps-2 text-sm " for="active">
                                        <input class="hidden" value="active" type="radio" name="filter" id="active" wire:model="filter">
                                        <div class="z-10 relative text-white label-checked:active">{{ __('Active') }}</div>
                                    </label>
                                </div>
                                <div class="form-check mx-2 mt-2">
                                    <label class="form-check-label ps-2 text-sm" for="waiting">
                                        <input class="hidden" value="waiting" type="radio" name="filter" id="waiting" wire:model="filter">
                                        <div class="z-10 relative text-white label-checked:active">{{ __('Waiting') }}</div>
                                    </label>
                                </div>
                                <div class="form-check mx-2 mt-2">
                                    <label class="form-check-label ps-2 py-1 text-sm" for="handled">
                                        <input checked class="hidden" value="handled" type="radio" name="filter" id="handled" wire:model="filter">
                                        <div class="z-10 relative text-white label-checked:active">{{ __('Handled') }}</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-auto md:h-3/4 sm:h-1/2">
                    <input class="w-full dark:bg-slate-800" type="text" placeholder="search" name="search" id="search" wire:model="search">
                    <div wire:poll.visible>
                        <!-- //TICKET -->
                        @foreach ($tickets as $ticket)
                            <a x-on:click="window.scrollBy(0, $refs.blue.getBoundingClientRect().top - 50" wire:click="chatCustomer('{{ Hashids::encode($ticket->request->client->id) }}')" class="cursor-pointer client-click">
                                <div class="text-sm whitespace-no-wrap border-l-8 border-red-600">
                                    <div class="md:flex px-2 py-1 hover:bg-gray-200 border-b-2 border-gray-400 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-gray-100 focus:ring-opacity-50">
                                        <div class="sm:max-w-sm sm:flex-none md:w-auto md:flex-auto flex-col items-start relative z-10 p-1">
                                            <div class="flex">
                                                <div class="flex-grow">Ticket #{{$ticket->request_id}}<br><small>{{$ticket->reasons}}</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach

                        <!-- //CHAT -->
                        @foreach ($data as $item)
                            <a x-on:click="window.scrollBy(0, $refs.blue.getBoundingClientRect().top - 50" wire:click="chatCustomer('{{ Hashids::encode($item->id) }}')" class="cursor-pointer client-click">
                                <div class="text-sm whitespace-no-wrap {{auth()->user()->chatsession && auth()->user()->chatsession->client_id==$item->id?'border-l-8 border-green-600':''}}">
                                    <div class="md:flex px-2 py-2 {{$client_id!=0 && $client_id==$item->id?'bg-gray-300 dark:bg-slate-800':'bg-white dark:bg-slate-700'}} hover:bg-gray-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-gray-100 focus:ring-opacity-50">
                                        <div class="md:h-auto text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" src="{{ $item->profile_photo_url }}" alt="{{ $item->name }}" />
                                        </div>
                                        <div class="sm:max-w-sm sm:flex-none md:w-auto md:flex-auto flex-col items-start relative z-10 p-2 xl:p-2">
                                            <div class="flex">
                                                <div class="flex-grow">{{ $item->name }}</div>
                                                @if($item->active && countMsg($item)>0)
                                                <div class="flex w-auto text-center">
                                                    <div class="px-1 w-auto h-4 m-1 rounded-full bg-red-500 text-xs text-gray-50 text-center">{{countMsg($item)}}</div>
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
                </div>
                <!-- <x-jet-nav-link href="#close">
                    {{ __('Closed') }}
                </x-jet-nav-link> -->
            </div>

            <!-- Area Chatting -->
            <div x-ref="blue" id="chatArea" class="bg-gray-200 dark:bg-slate-600 overflow-hidden shadow-xl mt-2 sm:rounded-sm col-span-7 bg-blend-darken h-auto">
                @livewire('chat-box', ['client_id' => $client_id ?? @request('id')], key($client_id))
            </div>

            <!-- Detail Client -->
            @if($client)
                <div class="hidden md:inline bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-sm col-span-2 mt-2">
                    <div class="p-1">
                        <div class="bg-gray-400 dark:bg-slate-700 lg:10">
                            <div class="w-full mx-auto p-1 px-1">
                                <div class="flex items-center justify-between flex-wrap">
                                    <div class="w-0 flex-1 flex items-center">
                                        <p class="ml-1 font-medium text-white truncate">
                                            <span class="hidden md:inline">
                                                Customer Info
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-1">
                            <input class="border-gray-300 focus:border-indigo-300 w-full my-1 text-xs dark:bg-slate-700" type="text"  placeholder="{{ __('Customer Phone') }}"
                                x-ref="phone"
                                wire:model.defer="client_phone"
                                readonly disabled>
                            <input class="border-gray-300 focus:border-indigo-300 w-full my-1 text-xs dark:bg-slate-700" type="text" placeholder="{{ __('Customer Name') }}"
                                x-ref="client_name"
                                wire:model.defer="client_name">
                            <textarea class="border-gray-300 focus:border-indigo-300 w-full my-1 text-xs dark:bg-slate-700" id="note"
                                class="mt-1 block w-full mb-4"
                                placeholder="{{ __('write note here') }}"
                                wire:model.defer="note"
                                wire:model="note"></textarea>
                            <input class="border-gray-300 focus:border-indigo-300 w-full text-xs dark:bg-slate-700" type="text" placeholder="{{ __('Add tag here') }}"
                                x-ref="tag"
                                wire:model.defer="tag">
                            <div class="my-1 text-right">
                                <button class="bg-gray-100 border border-gray-300 px-4 py-1 dark:bg-slate-700" wire:click="save">
                                    {{__('Save')}}
                                </button>
                                <x-jet-action-message class="mr-3" on="saved">
                                    {{ __('Detail customer saved.') }}
                                </x-jet-action-message>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="{{$client?'block':'hidden'}}">
                <div class="{{count($dataTemplate['quick'])==0?'hidden':''}} lg:absolute lg:bottom-24 bottom-1 lg:w-2/4 w-100">
                    <div class="relative z-10 w-full mt-1 bg-gray-200 rounded-md shadow-lg top-0 border-2 border-gray-400">
                        <ul class="p-0 overflow-auto h-auto max-h-screen text-base leading-6 rounded-md shadow-xs focus:outline-none sm:text-sm sm:leading-5">
                            @foreach ($dataTemplate['quick'] as $quick)
                                <li role="option" class="relative text-gray-900 cursor-default select-none border border-gray-300">
                                    <button wire:click="showQuickModal({{$quick->id}})" class="py-2 pl-3 text-xs text-gray-900 cursor-default select-none pr-9 w-full text-left focus:text-white focus:bg-indigo-600 hover:text-white hover:bg-indigo-600">
                                        {{$quick->name}} :
                                        @foreach ($quick->actions as $action)
                                            {{$action->message}}
                                        @endforeach
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @if($handlingSession && $client && $client_id)
                <div class="{{$handlingSession->client_id!=$client_id?'block':'hidden'}} py-10 z-10 md:static sm:fixed sm:inset-x-0 sm:bottom-0 lg:fixed lg:bottom-0 dark:bg-yellow-800">
                    <div id="msg_session">
                        <div class="justify-center w-100 flex col-span-12"></div>
                    </div>
                </div>
                @endif
                <div id="texting-area" class="{{$handlingSession && $handlingSession->client_id==$client_id?'block':'hidden'}} py-3 grid grid-cols-8 z-10 md:static sm:fixed sm:inset-x-0 sm:bottom-0 lg:fixed lg:bottom-0 dark:bg-slate-800">
                    <div class="flex items-center justify-center col-span-1 align-text-bottom">
                        <button class="cursor-pointer text-sm text-grey-500 p-2" wire:click="actionShowModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </button>
                    </div>
                    <div data-emojiarea data-type="unicode" data-global-picker="false" class="flex py-2 col-span-6 pr-2" >
                        <div class="flex items-center col-span-1 align-text-bottom emoji-button cursor-pointer text-sm text-grey-500 dark:text-slate-300 p-1">
                            <button class="cursor-pointer text-sm text-grey-500 dark:text-slate-300 p-1" >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                                </svg>
                            </button>
                        </div>
                        <x-textarea
                            id="message"
                            class="mt-1 block w-full h-full text-lg dark:bg-slate-900"
                            placeholder="{{ __('write a reply...') }}"
                            wire:model="message"
                        />
                    </div>
                    <div class="flex items-center justify-center col-span-1 align-text-bottom">
                        <button class="p-2 sm:p-2 md:p-4 lg:p-4 bg-green-600 text-white rounded-full" wire:click="sendMessage">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 rotate-30" viewBox="0 0 20 20" fill="currentColor">
                                    <path style="transform: rotate(90deg);transform-origin: 50% 50%;" d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="{{$handlingSession?'hidden':'block'}} py-6 z-10 md:static sm:fixed sm:inset-x-0 sm:bottom-0 lg:fixed lg:bottom-0 dark:bg-slate-800">
                    <div id="handle_session">
                        <div class="justify-center w-100 flex col-span-12">
                            <button class="bg-green-600 text-white border border-gray-300 px-8 py-4 text-lg" wire:click="joinChat">
                                {{__('Join Conversation')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attachment Modal -->
    <x-jet-dialog-modal wire:model="modalAttachment">
        <x-slot name="title">
            {{ __('Attachment message') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                    <!-- Profile Photo File Input -->
                    <input type="file"
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

                    <!-- New Profile Photo Preview -->
                    <div class="mt-2" x-show="photoPreview">
                        <span class="block h-48 w-full"
                            x-bind:style="'background-size: contain; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <x-jet-input-error for="photo" class="mt-2" />
                </div>
                @if(!$photo)
                <x-jet-label for="photo" value="{{ __('Link') }}" />
                <input class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 w-full my-1 text-sm" type="text" placeholder="{{ __('Link Attachment') }}"
                            x-ref="link_attachment"
                            wire:model.defer="link_attachment">
                @endif
                <x-jet-label for="photo" value="{{ __('Caption') }}" />
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

    @push('chat-waweb')
        <script>
            $(function(){
                /**
                 * Returns the chat message proper format
                 *
                 * @param {string} id
                 * @param {string} username
                 * @param {string} message
                 */
                function messageFormat(id, name, message) {
                    let userId = "1";
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
                var waConnection = clientSocket({ port: 3287 });
                // The messageBox element
                var messageBox = $("#messageBox");
                // The message element
                var message = $("#message");
                // lastest session wa
                var waSession = "{{ auth()->user()->currentTeam->waWeb ? json_encode(auth()->user()->currentTeam->waWeb->session) : 0 }}";

                /**
                 * When the connection is open
                 */
                waConnection.onopen = function () {
                    console.log("Check WA connection is open on Blade");
                    // Send the information of the client user here
                    // console.log("session", @json($session));

                    // Check if has session connection WA before
                    if(waSession != 0){
                        // console.log("send session");
                        // console.log(waSession);
                        waConnection.send(
                            JSON.stringify({
                                type: "sendSession",
                                data: {
                                    team_id: "{{ auth()->user()->currentTeam->id }}",
                                    session: @json($session)
                                }
                            })
                        );
                    }
                }

                /**
                 * Will receive messages from the websocket server
                 * and show it to blade
                 */
                waConnection.onmessage = function(message){
                    var result = JSON.parse(message.data);
                    console.log(result);
                    var dataResponse = result.data;
                    if (result.type == "MSG") {
                        console.log("will save response", dataResponse);
                        @this.call('saveResponse', dataResponse.details);

                        // messageBox.append(messageFormat(
                        //     dataResponse.team_id,
                        //     dataResponse.team_name,
                        //     dataResponse.message
                        // ));
                    }

                    if(result.type == "WA_READY"){
                        console.log("WA READY", dataResponse);
                    }

                    if(result.type == "WA_FAILURE"){
                        console.log("WA FAILURE", dataResponse);
                        //store api wa session here
                        @this.call('isFail');
                    }
                }

                /**
                 * Send the prompt to the websocket server
                 */
                window.addEventListener('chat-send-message', event => {
                    console.log("Send message", event.detail);

                    waConnection.send(JSON.stringify({
                        type: "sendMessage",
                        data: event.detail
                    }));
                });

            });
        </script>
    @endpush
    <link rel="stylesheet" href="{{ url('js/emoji/docs/assets/css/style.css') }}">
</div>
