<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-slate-600">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-2 py-4 bg-white dark:bg-slate-700 shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
