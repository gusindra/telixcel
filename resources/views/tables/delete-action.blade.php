<div x-data="{ open: false }" class="inline-flex">
    <button type="button" x-on:click="open = true" class="tx-row-link tx-row-link-danger">
        Delete
    </button>

    <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0">
        <div class="fixed inset-0 bg-gray-500 opacity-75" x-on:click="open = false"></div>

        <div class="relative mb-6 bg-white dark:bg-slate-700 rounded-lg overflow-hidden shadow-xl sm:w-full sm:max-w-md sm:mx-auto">
            <div class="px-6 py-4">
                <div class="text-lg text-gray-900 dark:text-slate-100">
                    Delete Confirmation
                </div>

                <div class="mt-4 text-sm text-gray-600 dark:text-slate-300">
                    Are you sure you want to delete this {{ $label ?? 'data' }}?
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-100 dark:bg-slate-900 text-right">
                <button type="button" x-on:click="open = false" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none disabled:opacity-25 transition">
                    Cancel
                </button>

                <form method="POST" action="{{ route('records.destroy', [$type, $id]) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none disabled:opacity-25 transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
