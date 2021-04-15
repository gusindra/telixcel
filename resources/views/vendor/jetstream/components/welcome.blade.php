<div class="p-6 sm:px-20 bg-white border-b border-gray-200">
    <div>
        <!-- <x-jet-application-logo class="block h-12 w-auto" /> -->
    </div>

    <div class="mt-8 text-2xl">
        Welcome to your Telixcel application!
    </div>

    <div class="mt-6 text-gray-500">
        Telixcel provides a beautiful, robust starting point for your next application. Telixcel is designed
        to help you build your client notification via Whatsapp using a environment that is simple, powerful, and enjoyable.  We hope you love it.
    </div>
</div>

<div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-3">
    <div class="p-6">
            <div class="flex items-center">
                <a href="{{ route('client') }}">
                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-20 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </a>
                <div class="ml-4 text-lg text-gray-600 leading-2 font-semibold text-3xl">
                    <span>100</span>
                    <div class="mt-2 text-sm text-gray-500">
                        <a href="{{ route('client') }}">Client</a>
                    </div>
                </div>
            </div>
    </div>

    <div class="p-6">
        <div class="flex items-center">
            <a href="{{ route('message') }}">
                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-20 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
            </a>
            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold text-3xl">
                <span>3278</span>
                <div class="mt-2 text-sm text-gray-500">
                    <a href="{{ route('message') }}">Out Bound</a>
                </div>
            </div>
        </div>

        <div class="ml-12">
        </div>
    </div>

    <div class="p-6">
        <div class="flex items-center">
            <a href="{{ route('message') }}">
                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-20 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </a>
            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold text-3xl">
                <span>6431</span>
                <div class="mt-2 text-sm text-gray-500">
                    <a href="{{ route('message') }}"> In Bound</a>
                </div>
            </div>
        </div>
    </div>

</div>
