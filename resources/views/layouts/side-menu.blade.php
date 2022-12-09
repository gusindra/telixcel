<!--
=========================================================
* Dashboard Tailwind - v1.0.4
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard-tailwind
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

-->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png" />
        <link rel="icon" type="image/png" href="./assets/img/favicon.png" />
        <title>{{ config('app.name', 'Telixcel') }} @yield('title')</title>
        <!--     Fonts and icons     -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
        <!-- Font Awesome Icons -->
        <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
        <!-- Nucleo Icons -->
        <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
        <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
        <!-- Popper -->
        <script src="https://unpkg.com/@popperjs/core@2"></script>
        <!-- Main Styling -->
        <link rel="stylesheet" href="{{ url('css/app.css') }}">
        <link href="{{ url('css/soft-ui-dashboard-tailwind.min.css') }}" rel="stylesheet" />
        @livewireStyles
        <script src="{{ url('js/app.js') }}" defer></script>
        <link href="{{ url('assets/css/choices.css') }}" type="text/css" rel="stylesheet">
        @yield('header')
    </head>
    <body class="m-0 font-sans antialiased font-normal text-left leading-default text-base dark:bg-slate-950 bg-gray-50 text-slate-500 dark:text-white/80">
        <!-- Menu -->
        @include('navigation.side-nav')

        <!-- Content -->
        <main class="ease-soft-in-out xl:ml-68 relative h-full max-h-screen rounded-xl transition-all duration-200 z-40">
            @yield('content')
        </main>

        <!-- Setting -->
        <div fixed-plugin>
            <a fixed-plugin-button class="bottom-7 hidden right-7 text-xl z-990 shadow-soft-lg rounded-circle fixed cursor-pointer bg-white px-4 py-2 text-slate-700" aria-expanded="false">
                <i class="py-2 pointer-events-none fa fa-cog"> </i>
            </a>

            <div fixed-plugin-card class="dark:bg-gray-950/80 z-sticky shadow-soft-3xl w-90 ease-soft -right-90 fixed top-0 left-auto flex h-full min-w-0 flex-col break-words rounded-none border-0 bg-white/80 bg-clip-border px-2.5 backdrop-blur-2xl backdrop-saturate-200 duration-200">
                <div class="px-6 pt-4 pb-0 mb-0 border-b-0 rounded-t-2xl">
                    <div class="float-left">
                        <h5 class="mt-4 mb-0 dark:text-white">Admin Config</h5>
                        <!-- <p class="dark:text-white dark:opacity-60">See our dashboard options.</p> -->
                    </div>
                    <div class="float-right mt-6">
                        <button fixed-plugin-close-button class="inline-block p-0 mb-4 font-bold text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer hover:scale-102 leading-pro text-xs ease-soft-in tracking-tight-soft bg-150 bg-x-25 active:opacity-85 text-slate-700 dark:text-white">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>

                </div>
                <hr class="h-px mx-0 my-1 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                <div class="flex-auto p-6 pt-0 sm:pt-4">
                    <div class="max-w-full flex-0">
                        <!-- <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80" for="Currency">Team</label>
                        <select name="choices-sizes" choices-select>
                            <option value="Choice 1" selected="">USD</option>
                            <option value="Choice 2">EUR</option>
                            <option value="Choice 3">GBP</option>
                            <option value="Choice 4">CNY</option>
                            <option value="Choice 5">INR</option>
                            <option value="Choice 6">BTC</option>
                        </select> -->
                        @livewire('switch-team')
                        <hr class="h-px mt-4 mb-1 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                        @livewire('switch-role')

                    </div>
                    <!-- <div class="mt-4">
                        <h6 class="mb-0 dark:text-white">Navbar Fixed</h6>
                    </div>
                    <div class="min-h-6 mb-0.5 block pl-0">
                        <input navbar-fixed-toggle class="rounded-10 duration-250 ease-soft-in-out after:rounded-circle after:shadow-soft-2xl after:duration-250 checked:after:translate-x-5.3 h-5 relative float-left mt-1 ml-auto w-10 cursor-pointer appearance-none border border-solid border-gray-200 bg-slate-800/10 bg-none bg-contain bg-left bg-no-repeat align-top transition-all after:absolute after:top-px after:h-4 after:w-4 after:translate-x-px after:bg-white after:content-[''] checked:border-slate-800/95 checked:bg-slate-800/95 checked:bg-none checked:bg-right" type="checkbox" />
                    </div>-->
                    <hr class="h-px mt-4 mb-1 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                    <div class="mt-2">
                        <h6 class="block py-2 text-xs text-gray-400">Sidenav Mini</h6>
                    </div>
                    <div class="min-h-6 mb-0.5 block pl-0">
                        <input id="sidenav-mini-toggle" sidenav-mini-toggle class="rounded-10 duration-250 ease-soft-in-out after:rounded-circle after:shadow-soft-2xl after:duration-250 checked:after:translate-x-5.3 h-5 relative float-left mt-1 ml-auto w-10 cursor-pointer appearance-none border border-solid border-gray-200 bg-slate-800/10 bg-none bg-contain bg-left bg-no-repeat align-top transition-all after:absolute after:top-px after:h-4 after:w-4 after:translate-x-px after:bg-white after:content-[''] checked:border-slate-800/95 checked:bg-slate-800/95 checked:bg-none checked:bg-right" type="checkbox" />
                    </div>
                    <hr class="h-px mt-4 mb-1 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                    <div class="mt-2">
                        <h6 class="block py-2 text-xs text-gray-400">Light/Dark</h6>
                    </div>
                    <div class="min-h-6 mb-0.5 block pl-0">
                        <input id="dark-toggle" dark-toggle class="rounded-10 duration-250 ease-soft-in-out after:rounded-circle after:shadow-soft-2xl after:duration-250 checked:after:translate-x-5.3 h-5 relative float-left mt-1 ml-auto w-10 cursor-pointer appearance-none border border-solid border-gray-200 bg-slate-800/10 bg-none bg-contain bg-left bg-no-repeat align-top transition-all after:absolute after:top-px after:h-4 after:w-4 after:translate-x-px after:bg-white after:content-[''] checked:border-slate-800/95 checked:bg-slate-800/95 checked:bg-none checked:bg-right" type="checkbox" />
                    </div>
                    <hr class="h-px my-6 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                    <a class="inline-block w-full px-6 py-3 mb-4 font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer leading-pro text-xs ease-soft-in hover:shadow-soft-xs hover:scale-102 active:opacity-85 tracking-tight-soft shadow-soft-md bg-150 bg-x-25 bg-gradient-to-tl from-blue-600 to-cyan-400" href="#" target="_blank">Upgrade Now</a>
                    <!-- <a class="inline-block w-full px-6 py-3 mb-4 font-bold text-center text-white uppercase align-middle transition-all bg-transparent border border-transparent border-solid rounded-lg cursor-pointer leading-pro text-xs ease-soft-in hover:shadow-soft-xs hover:scale-102 active:opacity-85 tracking-tight-soft shadow-soft-md bg-150 bg-x-25 bg-gradient-to-tl from-gray-900 to-slate-800 dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 dark:border-white dark:text-white" href="https://www.creative-tim.com/product/soft-ui-dashboard-tailwind" target="_blank">Free Demo</a> -->
                    <a class="inline-block w-full px-6 py-3 mb-4 font-bold text-center uppercase align-middle transition-all bg-transparent border border-solid rounded-lg shadow-none cursor-pointer active:shadow-soft-xs hover:scale-102 active:opacity-85 leading-pro text-xs ease-soft-in tracking-tight-soft bg-150 bg-x-25 border-slate-700 text-slate-700 hover:bg-transparent hover:text-slate-700 hover:shadow-none active:bg-slate-700 active:text-white active:hover:bg-transparent active:hover:text-slate-700 active:hover:shadow-none dark:border-white dark:text-white" href="#" target="_blank">View documentation</a>
                </div>
            </div>
        </div>

        <!-- SCRIPT -->
        <script src="{{ url('js/app.js') }}" defer></script>
        @livewireScripts
        <script src="{{ url('js/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ url('js/soft-ui-dashboard-pro-tailwind.min.js') }}?v=1.0.1"></script>
        <script src="{{ url('assets/js/choices.min.js') }}"></script>
        <script src="{{ url('assets/js/choices.js') }}" type="text/javascript" async="true"></script>
        <script>
            // Initial load of the page
            window.addEventListener("load", function() {
                var mode = 'false';
                //Check if User Has Set Pref from Application
                if (('dark' in localStorage)) {
                    switchMode(localStorage.dark)
                } else {
                    // User Has No Preference, So Get the Browser Mode ( set in Computer settings )
                    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        mode = 'false';
                    } else {
                        mode = 'false';
                    }
                    localStorage.dark = mode;
                    // Inform Livewire of the Mode so that It toggles the DarkMode set  in Tailwind.config.js
                    Livewire.emitTo('dark', 'ModeView', localStorage.dark)
                    switchMode(mode)
                }
            });

            function switchMode(mode) {
                if (localStorage.dark === 'true') {
                    document.documentElement.classList.add('dark')
                    document.getElementById('dark-toggle').checked = true;
                } else {
                    document.documentElement.classList.remove('dark')
                    document.getElementById('dark-toggle').checked = false;
                }
                Livewire.emitTo('dark', 'ModeView', mode)
            }

            // this emitted from Livewire to change the Class DarkMoe on and Off.
            window.addEventListener('view-mode', event => {
                localStorage.dark = event.detail.newMode;
                switchMode(event.detail.newMode);
            });
        </script>
    </body>
</html>
