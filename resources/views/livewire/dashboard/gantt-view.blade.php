<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <!-- Header Section — identical pattern to Dashboard -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gantt Chart</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Timeline — Main Task &amp; Sub Task progress</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('calendar.view') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Monthly View
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Project Info Cards -->
        @if(count($chartProjects) > 0)
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs text-gray-500 dark:text-gray-400">Klik salah satu project untuk menampilkan timeline-nya saja.</p>
                @if($selectedProjectId !== null)
                    <button wire:click="clearProjectFilter" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tampilkan semua project
                    </button>
                @endif
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($chartProjects as $idx => $proj)
                    @php
                        $accent = match($idx % 4) {
                            0 => 'border-l-blue-500',
                            1 => 'border-l-purple-500',
                            2 => 'border-l-teal-500',
                            3 => 'border-l-amber-500',
                        };
                        $initials = collect(explode(' ', $proj['name']))->map(fn($s) => mb_substr($s, 0, 1))->join('');
                    @endphp
                    <div wire:click="selectProject({{ $proj['id'] }})"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 {{ $accent }} p-4 cursor-pointer transition hover:shadow-lg {{ ($selectedProjectId !== null && (int) $selectedProjectId === (int) $proj['id']) ? 'ring-2 ring-indigo-500 ring-offset-1 dark:ring-offset-gray-800' : '' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex-shrink-0 w-9 h-9 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg flex items-center justify-center">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">{{ mb_substr($initials, 0, 2) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $proj['name'] }}</p>
                                @if($proj['type'])
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $proj['type'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Controls Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="inline-flex items-center gap-0.5 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        @foreach([3 => '3 Mo', 6 => '6 Mo', 12 => '1 Yr'] as $val => $label)
                            <button wire:click="setScale({{ $val }})"
                                class="px-3 py-1.5 rounded-md text-xs font-semibold transition {{ $scale == $val ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <div class="hidden md:flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#6366f1"></span>Project</span>
                        <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#9ca3af"></span>Pending</span>
                        <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#3b82f6"></span>Progress</span>
                        <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#22c55e"></span>Done</span>
                        <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#ef4444"></span>Overdue</span>
                    </div>
                </div>
                <div class="inline-flex items-center gap-1.5">
                    <button wire:click="prev" class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span class="px-3 py-1.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">{{ $windowLabel }}</span>
                    <button wire:click="next" class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button wire:click="today" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                        Today
                    </button>
                </div>
            </div>
            <div class="md:hidden flex flex-wrap items-center gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#6366f1"></span>Project</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#9ca3af"></span>Pending</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#3b82f6"></span>Progress</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#22c55e"></span>Done</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full" style="background:#ef4444"></span>Overdue</span>
            </div>
        </div>

        <!-- Gantt Chart Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if(count($rows) > 0)
                <div class="overflow-x-auto" x-data="{ expanded: {} }">
                    <div style="min-width: 800px;">
                        <!-- Headers -->
                        <div class="flex bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <div class="w-48 lg:w-56 xl:w-64 flex-shrink-0 px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                                Task
                            </div>
                            <div class="flex-1 relative h-10">
                                @foreach($months as $m)
                                    <div class="absolute top-0 h-full border-l border-gray-200 dark:border-gray-700 flex flex-col justify-center"
                                         style="left: {{ $m['left'] }}%; width: {{ $m['width'] }}%;">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 text-center">{{ $m['label'] }}</span>
                                        <span class="text-gray-400 dark:text-gray-500 text-center" style="font-size:9px;">{{ $m['year'] }}</span>
                                    </div>
                                @endforeach
                                @if($todayLeftPercent !== null)
                                    <div class="absolute top-0 h-full w-0.5 bg-red-500 z-20 shadow-sm" style="left: {{ $todayLeftPercent }}%;">
                                        <span class="absolute -top-1 left-1/2 -translate-x-1/2 bg-red-500 text-white font-bold px-1.5 py-0.5 rounded text-[9px] whitespace-nowrap">TODAY</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @foreach($rows as $row)
                            @if($row['kind'] === 'sub_task')
                                <div class="flex border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                                     x-show="expanded[{{ $row['root_task_id'] }}] !== false">
                            @else
                                <div class="flex border-b border-gray-100 dark:border-gray-700/50 {{ $row['kind'] === 'project' ? 'bg-indigo-50/40 dark:bg-indigo-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30' }} transition">
                            @endif
                                    <!-- Sidebar -->
                                    <div class="w-48 lg:w-56 xl:w-64 flex-shrink-0 py-2 pr-3 border-r border-gray-200 dark:border-gray-700 flex items-center gap-1.5"
                                         style="padding-left: {{ max(($row['depth'] - 1) * 16 + 10, 10) }}px;">
                                        @if($row['kind'] === 'main_task' && $row['has_children'])
                                            <button @click="expanded[{{ $row['task_id'] }}] = !expanded[{{ $row['task_id'] }}]"
                                                    class="flex-shrink-0 w-4 h-4 flex items-center justify-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                                <svg x-show="expanded[{{ $row['task_id'] }}] !== false" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                <svg x-show="expanded[{{ $row['task_id'] }}] === false" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                            </button>
                                        @elseif($row['kind'] === 'main_task')
                                            <span class="flex-shrink-0 w-4"></span>
                                        @endif

                                        @if($row['kind'] !== 'project')
                                            <span class="inline-block w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: {{ $row['color'] }};"></span>
                                        @endif
                                        <span class="truncate {{ $row['kind'] === 'project' ? 'font-bold text-xs text-gray-900 dark:text-white' : ($row['kind'] === 'main_task' ? 'font-medium text-xs text-gray-800 dark:text-gray-200' : 'text-xs text-gray-500 dark:text-gray-400') }}"
                                              title="{{ $row['label'] }}">{{ $row['label'] }}</span>
                                    </div>

                                    <!-- Timeline -->
                                    <div class="flex-1 relative h-8">
                                        @foreach($months as $m)
                                            <div class="absolute top-0 h-full border-l border-gray-50 dark:border-gray-700/30" style="left: {{ $m['left'] }}%;"></div>
                                        @endforeach
                                        @if($todayLeftPercent !== null)
                                            <div class="absolute top-0 h-full w-px bg-red-300/60 dark:bg-red-700/40 z-10" style="left: {{ $todayLeftPercent }}%;"></div>
                                        @endif
                                        @if($row['bar'])
                                            @php
                                                $tip = $row['label'];
                                                if ($row['assigned_to']) $tip .= '  |  Assigned: ' . $row['assigned_to'];
                                                if ($row['raw_start']) $tip .= '  |  Start: ' . $row['start'];
                                                if ($row['raw_end']) $tip .= '  |  Target: ' . $row['end'];
                                                if ($row['progress']) $tip .= '  |  Progress: ' . $row['progress'];
                                                if ($row['status']) $tip .= '  |  Status: ' . ucfirst($row['status']);
                                            @endphp
                                            <div class="absolute rounded-full {{ $row['kind'] === 'project' ? 'h-3.5 top-2 opacity-70' : 'h-2.5 top-[11px]' }}"
                                                 style="left: {{ $row['bar']['left'] }}%; width: {{ max($row['bar']['width'], 0.4) }}%; background: {{ $row['color'] }};"
                                                 title="{{ $tip }}"></div>
                                        @endif
                                    </div>
                                </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="px-6 py-20 text-center">
                    <svg class="w-14 h-14 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h12M4 12h8M4 18h14"/></svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">No tasks to display</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Tasks appear based on your active role and projects.</p>
                </div>
            @endif
        </div>
    </div>
</div>
