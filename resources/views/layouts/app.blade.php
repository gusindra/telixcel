<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-cloak 
      x-data="{darkMode: localStorage.getItem('dark') === 'true'}" 
      x-init="$watch('darkMode', val => localStorage.setItem('dark', val))" 
      :class="{ 'dark': darkMode }" 
      :data-dark="darkMode">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Telixcel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ url('backend/css/offcanvas.scss') }}">
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
    <link rel="stylesheet" href="{{ url('css/tail.css') }}">
    
    <!-- FIX: Uncommented and moved x-cloak style to head to prevent Alpine UI flickering -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <link rel="stylesheet" media="print" href="{{ url('backend/css/print.css') }}">
    @trixassets
    @livewireStyles

    <!-- Scripts -->
    <!-- WARNING: If js/app.js imports Alpine.js, REMOVE that import because Livewire v3 already includes Alpine! -->
    <script src="{{ url('js/app.js') }}" defer></script>
</head>

<body class="font-sans antialiased">
    <x-jet-banner />

    @php($embed = request()->boolean('embed'))
    <div class="min-h-screen bg-gray-100 dark:bg-slate-900">
        @unless($embed)
        <!-- Simplified Dark Mode Toggle Button -->
        <button x-cloak x-on:click="darkMode = !darkMode" class="inline-flex absolute right-10 md:right-0 m-5 z-50">
            <!-- Icon Moon -->
            <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <!-- Icon Sun -->
            <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>

        @livewire('navigation-menu')
        @endunless

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
            <div id="chat-event" class="mb-0 w-full flex md:flex-col bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-bl-2xl rounded-t-xl"></div>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
    @livewireChartsScripts
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    @script
    <script>
        // FIX 1: Use 'livewire:init' instead of 'window.load' so Livewire is defined before running
        document.addEventListener('livewire:init', () => {
            
            // Initial load of the page
            let mode = localStorage.getItem('dark') || 'false';

            // Check if User Has Set Pref from Application
            if (!('dark' in localStorage)) {
                // User Has No Preference, So Get the Browser Mode
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    mode = 'true'; 
                } else {
                    mode = 'false';
                }
                localStorage.setItem('dark', mode);
                switchMode(mode === 'true' || mode === true);
            } else {
                switchMode(localStorage.getItem('dark') === 'true');
            }

            // FIX 2: Use Livewire.on() instead of window.addEventListener for Livewire events
            Livewire.on('view-mode', event => {
                let newMode = event.newMode === 'true' || event.newMode === true;
                localStorage.setItem('dark', newMode);
                switchMode(newMode);
            });
        });

        function switchMode(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Update Alpine.js state so the Sun/Moon icons toggle immediately without page reload
            const rootEl = document.querySelector('[x-data]');
            if (rootEl && rootEl._x_dataStack) { 
                rootEl._x_dataStack[0].darkMode = isDark;
            }

            // FIX 3: Changed Livewire.emitTo() to Livewire.dispatchTo() for Livewire v3
            Livewire.dispatchTo('dark', 'ModeView', { mode: isDark });
        }
    </script>
    @endscript

    <audio id="sound" class="hidden" controls>
        <source src="{{url('/assets/sound/notif.wav')}}" type="audio/wav">
        Your browser does not support the audio element.
    </audio>
</body>

</html>