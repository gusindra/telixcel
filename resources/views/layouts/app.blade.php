<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link rel="stylesheet" href="{{ url('css/custom.css') }}">
        @trixassets
        @livewireStyles

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="hidden bg-white shadow">
                    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <div id="chat-event" class="mb-2 w-full flex md:flex-col bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-bl-2xl rounded-t-xl"></div>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ url('js/socket.js')}}"></script>
        @stack('chat-websocket')
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
                console.log("Reconnecting after 3 seconds...");
                setTimeout(() => {
                    window.location.reload();
                }, 3000)
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
    </body>
</html>
