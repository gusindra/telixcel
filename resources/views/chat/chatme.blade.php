<html lang="en" style="overflow:hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Telixcel</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&amp;display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="https://telixcel.s3.ap-southeast-1.amazonaws.com/assets/2022/app.min.css">
        <link rel="stylesheet" href="https://telixcel.s3.ap-southeast-1.amazonaws.com/assets/2022/tail.min.css">

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <style type="text/css"></style>
    </head>

    <body class="font-sans antialiased">
        <div  style="display: none;" x-show="show &amp;&amp; message" x-init="
                    document.addEventListener('banner-message', event => {
                        style = event.detail.style;
                        message = event.detail.message;
                        show = true;
                    });
                " class="bg-indigo-500">
            <div class="max-w-screen-xl mx-auto py-2 px-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center min-w-0">
                        <span class="flex p-2 rounded-lg bg-indigo-600" :class="{ 'bg-indigo-600': style == 'success', 'bg-red-600': style == 'danger' }">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>

                        <p class="ml-3 font-medium text-sm text-white truncate" x-text="message"></p>
                    </div>

                    <div class="flex-shrink-0 sm:ml-3">
                        <button type="button" class="-mr-1 flex p-2 rounded-md focus:outline-none sm:-mr-2 transition hover:bg-indigo-600 focus:bg-indigo-600" :class="{ 'hover:bg-indigo-600 focus:bg-indigo-600': style == 'success', 'hover:bg-red-600 focus:bg-red-600': style == 'danger' }" aria-label="Dismiss" x-on:click="show = false">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="min-h-screen  dark:bg-slate-600">
            <main>
                <div id="chat-event" class="mb-2 w-full flex md:flex-col bg-gradient-to-br rounded-bl-2xl rounded-t-xl"></div>
                <div>
                    <div class="">
                        <div>
                            <div class="items-center py-3 text-center">
                                <a href="{{route('chat.slug', $team->slug)}}" target="_blank" class="inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition px-4 py-3 pb-10">
                                    Chat Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
