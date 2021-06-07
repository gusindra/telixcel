<x-app-layout>
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <!-- <div class="bg-indigo-600 mb-4">
            <div class="w-full mx-auto p-3 px-3">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center">
                        <span class="flex p-2 rounded-lg bg-indigo-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        </span>
                        <p class="ml-3 font-medium text-white truncate">
                        <span class="md:hidden">
                            Testing Chat and Template
                        </span>
                        <span class="hidden md:inline">
                            You can make Testing Chat and Template here.
                        </span>
                        </p>
                    </div>
                    <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                        <a target="blank" href="{{route('chat.slug', auth()->user()->currentTeam->id)}}" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                            Test Chat
                        </a>
                    </div>
                    <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                        <button type="button" class="-mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2">
                    </button></div>
                </div>
            </div>
        </div> -->
    </div>
    <div class="">
        @livewire('chat-component')
    </div>
</x-app-layout>
