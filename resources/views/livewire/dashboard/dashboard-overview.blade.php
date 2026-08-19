<div class="tx-stack">
    <div class="tx-toolbar">
        <p class="tx-13 tx-fg-muted m-0">{{ __('Project overview') }}</p>
        <a href="{{ route('gantt.view') }}" class="tx-btn">{{ __('Gantt') }}</a>
        <a href="{{ route('calendar.view') }}" class="tx-btn tx-btn-ghost">{{ __('Calendar') }}</a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="tx-card tx-stat tx-stat-blue">
            <p class="tx-11 tx-fg-muted m-0">{{ __('Projects') }}</p>
            <p class="text-2xl font-semibold tx-stat-value mt-1 mb-0">{{ $totalProjects }}</p>
        </div>
        <div class="tx-card tx-stat tx-stat-slate">
            <p class="tx-11 tx-fg-muted m-0">{{ __('Tasks') }}</p>
            <p class="text-2xl font-semibold tx-stat-value mt-1 mb-0">{{ $totalTasks }}</p>
        </div>
        <div class="tx-card tx-stat tx-stat-rose">
            <p class="tx-11 tx-fg-muted m-0">{{ __('Pending') }}</p>
            <p class="text-2xl font-semibold tx-stat-value mt-1 mb-0">{{ $tasksByStatus['pending'] ?? 0 }}</p>
        </div>
        <div class="tx-card tx-stat tx-stat-amber">
            <p class="tx-11 tx-fg-muted m-0">{{ __('In Progress') }}</p>
            <p class="text-2xl font-semibold tx-stat-value mt-1 mb-0">{{ $tasksInProgress }}</p>
        </div>
        <div class="tx-card tx-stat tx-stat-green">
            <p class="tx-11 tx-fg-muted m-0">{{ __('Completed') }}</p>
            <p class="text-2xl font-semibold tx-stat-value mt-1 mb-0">{{ $tasksCompleted }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 {{ $selectedProject !== null ? 'xl:grid-cols-2' : '' }} gap-4">
        <x-page-section :title="__('Projects')">
            @if ($projectStats->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left tx-fg-muted">
                                <th class="py-2 pr-3 font-medium">{{ __('Project') }}</th>
                                <th class="py-2 px-3 font-medium text-center">{{ __('Tasks') }}</th>
                                <th class="py-2 px-3 font-medium">{{ __('Progress') }}</th>
                                <th class="py-2 pl-3 font-medium text-center">{{ __('Breakdown') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projectStats as $stat)
                                <tr wire:click="selectProject({{ $stat['id'] }})" class="cursor-pointer" style="box-shadow: inset 0 -1px 0 var(--tx-border);">
                                    <td class="py-2.5 pr-3">
                                        <span class="tx-project-link">{{ $stat['name'] }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center tx-fg">{{ $stat['total_tasks'] }}</td>
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-1.5 rounded-full" style="background: var(--tx-surface-1); min-width: 64px;">
                                                <div class="h-1.5 rounded-full" style="width: {{ $stat['progress_percentage'] }}%; background: var(--tx-primary);"></div>
                                            </div>
                                            <span class="tx-11 tx-fg-muted whitespace-nowrap">{{ $stat['progress_percentage'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 pl-3 text-center">
                                        <span class="tx-pill tx-pill-amber">{{ $stat['in_progress_tasks'] }} {{ __('progress') }}</span>
                                        <span class="tx-pill tx-pill-green">{{ $stat['completed_tasks'] }} {{ __('done') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            @php
                                $allCompleted = $projectStats->sum('completed_tasks');
                                $allInProgress = $projectStats->sum('in_progress_tasks');
                                $allTotal = $projectStats->sum('total_tasks');
                                $allProgress = $allTotal > 0 ? round(($allCompleted / $allTotal) * 100) : 0;
                            @endphp
                            <tr wire:click="loadProjectTasks(0, 'all')" class="cursor-pointer">
                                <td class="py-2.5 pr-3">
                                    <span class="tx-project-link">{{ __('All tasks') }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center tx-fg">{{ $allTotal }}</td>
                                <td class="py-2.5 px-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 rounded-full" style="background: var(--tx-surface-1); min-width: 64px;">
                                            <div class="h-1.5 rounded-full" style="width: {{ $allProgress }}%; background: var(--tx-primary);"></div>
                                        </div>
                                        <span class="tx-11 tx-fg-muted whitespace-nowrap">{{ $allProgress }}%</span>
                                    </div>
                                </td>
                                <td class="py-2.5 pl-3 text-center">
                                    <span class="tx-pill tx-pill-amber">{{ $allInProgress }} {{ __('progress') }}</span>
                                    <span class="tx-pill tx-pill-green">{{ $allCompleted }} {{ __('done') }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <p class="tx-13 tx-fg-muted m-0">{{ __('No projects yet.') }}</p>
                <a href="{{ route('project') }}" class="tx-btn mt-3">{{ __('Open projects') }}</a>
            @endif
        </x-page-section>

        @if ($selectedProject !== null && ($selectedProject == 0 || ($projectStats->isNotEmpty() && $projectStats->firstWhere('id', $selectedProject))))
            @php
                $selectedStat = $selectedProject == 0 ? null : $projectStats->firstWhere('id', $selectedProject);
            @endphp
            <x-page-section :title="$selectedStat ? $selectedStat['name'] : __('All tasks')">
                <x-slot name="toolbar">
                    <span></span>
                    <button type="button" wire:click="clearSelection()" class="tx-btn tx-btn-ghost">{{ __('Close') }}</button>
                </x-slot>

                @if ($selectedStat)
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-3">
                        <button type="button" wire:click="loadProjectTasks({{ $selectedProject }}, 'all')" class="tx-card tx-stat tx-stat-slate text-left">
                            <p class="tx-11 tx-fg-muted m-0">{{ __('Total') }}</p>
                            <p class="text-lg font-semibold tx-stat-value m-0">{{ $selectedStat['total_tasks'] }}</p>
                        </button>
                        <button type="button" wire:click="loadProjectTasks({{ $selectedProject }}, 'complete')" class="tx-card tx-stat tx-stat-green text-left">
                            <p class="tx-11 tx-fg-muted m-0">{{ __('Completed') }}</p>
                            <p class="text-lg font-semibold tx-stat-value m-0">{{ $selectedStat['completed_tasks'] }}</p>
                        </button>
                        <button type="button" wire:click="loadProjectTasks({{ $selectedProject }}, 'progress')" class="tx-card tx-stat tx-stat-amber text-left">
                            <p class="tx-11 tx-fg-muted m-0">{{ __('In Progress') }}</p>
                            <p class="text-lg font-semibold tx-stat-value m-0">{{ $selectedStat['in_progress_tasks'] }}</p>
                        </button>
                        <button type="button" wire:click="loadProjectTasks({{ $selectedProject }}, 'pending')" class="tx-card tx-stat tx-stat-rose text-left">
                            <p class="tx-11 tx-fg-muted m-0">{{ __('Pending') }}</p>
                            <p class="text-lg font-semibold tx-stat-value m-0">{{ $selectedStat['pending_tasks'] }}</p>
                        </button>
                    </div>
                @endif

                @if (count($selectedProjectTasks) > 0)
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach ($selectedProjectTasks as $task)
                            <div class="flex items-start justify-between gap-3 py-2" style="box-shadow: inset 0 -1px 0 var(--tx-border);">
                                <div class="min-w-0">
                                    <p class="tx-13 font-medium tx-fg m-0">{{ $task['title'] }}</p>
                                    <p class="tx-11 tx-fg-muted m-0 mt-0.5">
                                        {{ $task['owner_name'] }}
                                        @if ($task['target_date'])
                                            - {{ \Carbon\Carbon::parse($task['target_date'])->format('d M Y') }}
                                        @endif
                                        - {{ ucfirst($task['priority'] ?? 'medium') }}
                                        - {{ ucfirst($task['status']) }}
                                    </p>
                                </div>
                                @if ($task['status'] !== 'complete')
                                    <input type="checkbox" class="mt-1" wire:click="setStatus({{ $task['id'] }})" title="{{ __('Mark done') }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="tx-13 tx-fg-muted m-0">{{ __('No tasks in this filter.') }}</p>
                @endif
            </x-page-section>
        @endif
    </div>
</div>
