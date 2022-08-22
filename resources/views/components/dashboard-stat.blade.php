<div class="p-6">
    <div class="flex items-center">
        <a href="{{ route('client') }}">
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </a>
        <div class="ml-4 text-gray-600 dark:text-gray-300 leading-2 font-semibold text-3xl">
            <span>{{$client}}</span>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                <a href="{{ route('client') }}">Client</a>
            </div>
        </div>
    </div>
</div>

<div class="p-6">
    <div class="flex items-center">
        <a href="{{ route('message') }}">
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
        </a>
        <div class="ml-4 text-gray-600 dark:text-gray-300 leading-7 font-semibold text-3xl">
            <span>{{$outbound}}</span>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
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
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
        </a>
        <div class="ml-4 text-gray-600 dark:text-gray-300 leading-7 font-semibold text-3xl">
            <span>{{$inbound}}</span>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                <a href="{{ route('message') }}"> In Bound</a>
            </div>
        </div>
    </div>
</div>

<div class="p-6">
    <div class="flex items-center">
        <a href="{{ route('message') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
            </svg>
        </a>
        <div class="ml-4 text-gray-600 dark:text-gray-300 leading-7 font-semibold text-3xl">
            <span>{{$api}}</span>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                <a href="{{ route('message') }}"> API Call</a>
            </div>
        </div>
    </div>
</div>
