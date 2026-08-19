@props(['id' => null, 'maxWidth' => null])

<x-jet-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="bg-white dark:bg-slate-700 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="flex items-start">
            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <div class="ml-4 text-left">
                <h3 class="text-lg">
                    {{ $title }}
                </h3>

                <div class="mt-2">
                    {{ $content }}
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-100 dark:bg-slate-900 flex flex-wrap items-center justify-end gap-2">
        {{ $footer }}
    </div>
</x-jet-modal>
