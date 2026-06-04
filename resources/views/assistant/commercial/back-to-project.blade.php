@if(request('source') === 'project' && request('id'))
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-12 pt-3">
        <nav class="flex items-center text-sm text-gray-500 dark:text-slate-400">
            <a href="{{ url('/project/'.request('id')) }}"
               class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back to project') }}
            </a>
            <span class="mx-2 text-gray-300">/</span>
            <span>{{ __('Commercial') }}</span>
        </nav>
    </div>
@endif
