<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Project Tasks Calendar</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">View all project tasks by month</p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            <!-- Calendar Header with Navigation -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <button wire:click="previousMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y') }}
                        </h2>
                    </div>

                    <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <button wire:click="jumpToToday" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Today
                </button>
            </div>

            <!-- Weekday Headers -->
            <div class="grid grid-cols-7 gap-2 mb-4">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <p class="font-semibold text-gray-700 dark:text-gray-300 text-sm">{{ $day }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-2 auto-rows-max">
                @foreach($calendarDays as $dayData)
                    @php
                        $dateString = $dayData['date']->format('Y-m-d');
                        $tasksForDate = $monthTasks[$dateString] ?? [];
                        $isCurrentMonth = $dayData['currentMonth'];
                        $isToday = $dayData['isToday'];
                    @endphp

                    <div class="min-h-32 p-2 rounded-lg border-2 transition-all 
                        {{ $isToday ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}
                        {{ !$isCurrentMonth ? 'bg-gray-50 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-800' }}">

                        <!-- Day Number -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold px-2 py-1 rounded
                                {{ $isToday ? 'bg-blue-500 text-white' : '' }}
                                {{ !$isCurrentMonth ? 'text-gray-400 dark:text-gray-600' : 'text-gray-900 dark:text-gray-200' }}">
                                {{ $dayData['day'] }}
                            </span>
                            @if($isToday)
                                <span class="text-xs bg-blue-500 text-white px-2 py-1 rounded">Today</span>
                            @endif
                        </div>

                        <!-- Tasks List -->
                        @if(count($tasksForDate) > 0)
                            <div class="space-y-1">
                                @foreach($tasksForDate as $task)
                                    <div class="text-xs p-1 rounded truncate cursor-pointer hover:opacity-80 transition-opacity {{ $this->getStatusColor($task['status']) }}" 
                                         title="{{ $task['title'] }} - {{ ucfirst($task['status']) }}">
                                        <span class="font-semibold mr-1">{{ $this->getStatusLabel($task['status']) }}</span>
                                        <span class="truncate">{{ substr($task['title'], 0, 20) }}</span>
                                        @if(strlen($task['title']) > 20)...@endif
                                    </div>
                                @endforeach

                                @if(count($tasksForDate) > 3)
                                    <div class="text-xs text-gray-600 dark:text-gray-400 px-1 py-1">
                                        +{{ count($tasksForDate) - 3 }} more
                                    </div>
                                @endif
                            </div>
                        @else
                            @if($isCurrentMonth)
                                <div class="text-xs text-gray-400 dark:text-gray-600 italic">No tasks</div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Legend -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Status Legend</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            ▶ In Progress
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            ⏳ Pending
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            ✓ Completed
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                            • Other
                        </span>
                    </div>
                </div>
            </div>

            <!-- Task Summary for Month -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Tasks Summary</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $totalTasks = 0;
                        $progressTasks = 0;
                        $pendingTasks = 0;
                        $completeTasks = 0;

                        foreach($monthTasks as $dateTasks) {
                            foreach($dateTasks as $task) {
                                $totalTasks++;
                                if($task['status'] === 'progress') $progressTasks++;
                                elseif($task['status'] === 'pending') $pendingTasks++;
                                elseif($task['status'] === 'complete') $completeTasks++;
                            }
                        }
                    @endphp

                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalTasks }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Tasks</div>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $progressTasks }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">In Progress</div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $pendingTasks }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pending</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $completeTasks }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Completed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
