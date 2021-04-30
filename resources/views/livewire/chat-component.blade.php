<div>
    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg col-span-2">
                <div class="p-3">
                    <x-jet-nav-link href="#active" >
                        {{ __('Active') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="#waiting" >
                        {{ __('Waiting') }}
                    </x-jet-nav-link>
                </div>
                <div class="overflow-auto h-3/4">
                    @foreach ($data as $item)
                        <a wire:click="chatCustomer('{{ $item->id }}')" class="cursor-pointer">
                            <div class="text-sm whitespace-no-wrap">
                                <div class="md:flex px-2 py-2 {{$client_id!=0 && $client_id==$item->id?'bg-gray-300':'bg-white'}} hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 focus:ring-opacity-50">
                                    <div class="md:h-auto text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $item->profile_photo_url }}" alt="{{ $item->name }}" />
                                    </div>
                                    <div class="sm:max-w-sm sm:flex-none md:w-auto md:flex-auto flex-col items-start relative z-10 p-2 xl:p-2">
                                        {{ $item->name }}
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
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg col-span-7 bg-blend-darken ">
                @if($client)
                    @livewire('chat-box', ['client_id' => $client_id], key($client_id))
                @endif
            </div>
            @if($client)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 col-span-3">
                <x-jet-input type="text" class="mt-1 block w-full mb-4" placeholder="{{ __('Customer Name') }}"
                    x-ref="name"
                    wire:model.defer="client_name"
                    wire:keydown.enter="customerName" />
                <x-jet-input type="text" class="mt-1 block w-full mb-4" placeholder="{{ __('Customer Phone') }}"
                    x-ref="phone"
                    wire:model.defer="client_phone"
                    wire:keydown.enter="customerPhone" />
                <x-textarea
                    id="note"
                    class="mt-1 block w-full mb-4"
                    placeholder="{{ __('note') }}"
                    wire:model="note" />
                <x-jet-input type="text" class="mt-1 block w-full mb-4" placeholder="{{ __('Tag') }}"
                    x-ref="tag"
                    wire:model.defer="tag"
                    wire:keydown.enter="tag" />
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
                   window.location.reload();
                });
            });
        </script>
    @endpush
</div>
