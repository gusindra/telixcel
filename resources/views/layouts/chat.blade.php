<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-cloak
    x-init="$watch('darkMode', (val) => localStorage.setItem('dark',val))"
    x-data="{darkMode: localStorage.getItem('dark')}"
    :class="darkMode==='true' || darkMode==true ? 'dark' : ''"
    :data-dark="darkMode">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Telixcel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ url('css/app.css') }}">
        <link rel="stylesheet" href="{{ url('css/tail.css') }}">
        <link rel="stylesheet" href="{{ url('js/emoji/docs/assets/css/style.css') }}">
        @trixassets
        @livewireStyles

        <!-- Scripts -->
        <script src="{{ url('js/app.js') }}" defer></script>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script type="module" src="{{ url('js/emoji/docs/assets/js/jquery.emojiarea.min.js') }}"></script>
    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />

        <div class="min-h-screen bg-gray-100 dark:bg-slate-600">

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
        <script src="{{ url('backend/js/socket.js')}}"></script>
        <!-- @stack('chat-websocket') -->
        <script>
            $('#input1').on('input', function() {
                $('#value1').text($('#input1').val());
            });
            $('#value1').text($('#input1').val());
        </script>

    </body>
</html>
