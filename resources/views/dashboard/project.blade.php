<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Projects --}}
        <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-slate-500">
                <h3 class="text-lg font-medium text-gray-900 dark:text-slate-200">{{ __('Projects') }}</h3>
                <a href="{{ route('project') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('View all') }}</a>
            </div>
            <div class="px-4 py-2">
                <livewire:table.project-table searchable="name" exportable :key="'dash-project-table'" />
            </div>
        </div>

        {{-- Tasks (who is working on what) --}}
        <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-4 border-b border-gray-200 dark:border-slate-500">
                <h3 class="text-lg font-medium text-gray-900 dark:text-slate-200">{{ __('Tasks') }}</h3>
                <p class="text-sm text-gray-500 dark:text-slate-300">{{ __('Tasks and who is working on them.') }}</p>
            </div>
            <div class="px-4 py-3">
                @livewire('task.todo', ['ownerId' => $ownerId ?? null], key('dash-todo-'.($ownerId ?? 'all')))
            </div>
        </div>

    </div>
</div>
