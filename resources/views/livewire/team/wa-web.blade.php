<div>
    <x-jet-section-border />

    <x-jet-form-section submit="connect" class="mt-10 sm:mt-0 hide hidden">
        <x-slot name="title">
            {{ __('WhatsApp Connection') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The whatsapp connection information.') }}
        </x-slot>

        <x-slot name="form">
            <!-- Team Owner Information -->
            <div class="col-span-3">
                <x-jet-label value="{{ __('Scan Whatsapp Barcode') }}" />

                <div class="items-center mt-2">
                @if($ready==false)
                    @if($barcode!=0)
                    <img class="w-full" src="data:image/png;base64,{{DNS2D::getBarcodePNG($barcode, 'QRCODE')}}" alt="barcode" /><br><br>
                    @else
                    <span>Click "Connect WA Web" to start scan Barcode</span>
                    @endif
                    <input id="barcode" wire:model="barcode" type="hidden" value="1@qM97ICWOCwiIWseVjyi6cckL8G671VDBL83Y3JB179ixU9AdNOY+IKkQQ/9rp21Gs6GCUY19SQ/OUQ==,pVRePEJeu8SRJ5haZT/XYM7wV+34jc9ojTOU67d1FFA=,LK70vCly6ur5Mi4TZIUEwA==" />
                @else
                    <span>WhatsApp Web is Ready to Use</span>
                @endif
                </div>

                <div id="chat-event"></div>
                <div id="messageBox"></div>

            </div>
            <div class="col-span-3">
                <x-jet-label value="{{ __('Last connected') }}" />
                @if($wa_session)
                <div class="items-center mt-2">
                    <div class="ml-4 leading-tight">
                        <div>{{ $wa_session->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endif
            </div>
            @if($wa_session)
            <div class="col-span-3">
                <button class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-600 disabled:opacity-25 transition" wire:click="rescan">
                    {{ __('Close Session') }}
                </button>
            </div>
            @endif
            <button wire:click="saveSession(1)">Save Session</button>

        </x-slot>

        @if (Gate::check('update', $team))
            <x-slot name="actions">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Saved.') }}
                </x-jet-action-message>

                @if(!$wa_session)
                    <x-jet-button>
                        {{ __('Connect WA Web') }}
                    </x-jet-button>
                @endif
            </x-slot>
        @else
            <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="reload">
                {{ __('Re-Scan') }}
            </button>
        @endif

    </x-jet-form-section>


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
                var waSession = "{{$team->waWeb}}";

                /**
                 * When the connection is open
                 */
                waConnection.onopen = function () {
                    console.log("Check WA connection is open on Blade");
                    // Send the information of the client user here

                    // Check if has session connection WA before
                    if(waSession){
                        waConnection.send(
                            JSON.stringify({
                                type: "session",
                                data: {
                                    session: "{{$wa_session->session ?? 0}}",
                                    team: "{{$team->id}}"
                                }
                            })
                        );
                    }
                }

                /**
                 * Will receive messages from the websocket server
                 */
                waConnection.onmessage = function(message){
                    var result = JSON.parse(message.data);
                    console.log(result);
                    var dataResponse = result.data;
                    if (result.type == "chatMessage") {
                        messageBox.append(messageFormat(
                            dataResponse.team_id,
                            dataResponse.team_name,
                            dataResponse.session
                        ));
                    }

                    if(result.type == "WA_SESSION"){
                        console.log("STORE WA SESSION", dataResponse);
                        //store api wa session here
                        @this.call('saveSession', dataResponse);
                    }

                    if(result.type == "QR"){
                        console.log("Barcode QR", dataResponse.qr);
                        @this.call('setBarcode', dataResponse.qr);

                        $('#barcode').val(dataResponse.qr);
                        if(dataResponse.attempt==0){
                            console.log("Please click refresh again to scan new code");
                        }
                        //store api wa session here
                    }

                    if(result.type == "WA_READY"){
                        console.log("WA READY", dataResponse);
                        //store api wa session here
                        @this.call('isReady');
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
                window.addEventListener('send-session-wa', event => {
                    console.log("Send session", event.detail);
                    waConnection.send(JSON.stringify({
                        type: "sendSession",
                        data: event.detail
                    }));
                });

                /**
                 * Send the prompt to the websocket server
                 */
                window.addEventListener('request-session-wa', event => {
                    console.log("Request session", event.detail);
                    waConnection.send(JSON.stringify({
                        type: "requestSession",
                        data: event.detail
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
    @endpush
</div>
