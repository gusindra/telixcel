<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back! Here's your project overview.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last updated: <span class="text-gray-700 dark:text-gray-200">Just now</span></p>
                    </div>
                    <a href="{{ route('calendar.view') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-md">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Monthly View</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Projects Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Projects</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalProjects }}</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                        <i class="fas fa-folder text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Tasks Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Tasks</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalTasks }}</p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-lg">
                        <i class="fas fa-tasks text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- In Progress Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">In Progress</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $tasksInProgress }}</p>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-lg">
                        <i class="fas fa-spinner text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $tasksCompleted }}</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Status Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $status => $label)
                @php
                    $statusKey = $status === 'in_progress' ? 'in_progress' : ($status === 'completed' ? 'completed' : 'pending');
                    $count = $tasksByStatus[$statusKey] ?? 0;
                    $colors = [
                        'pending' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'icon' => 'fas fa-clock', 'iconBg' => 'bg-gray-200 dark:bg-gray-600'],
                        'in_progress' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-300', 'icon' => 'fas fa-play-circle', 'iconBg' => 'bg-blue-200 dark:bg-blue-600'],
                        'completed' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-300', 'icon' => 'fas fa-check-circle', 'iconBg' => 'bg-green-200 dark:bg-green-600'],
                    ];
                    $color = $colors[$statusKey] ?? [];
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 {{ $color['bg'] }}">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="{{ $color['icon'] }} {{ $color['text'] }} text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $label }}</h3>
                            <p class="text-2xl font-semibold {{ $color['text'] }} mt-1">{{ $count }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex gap-2">
            <!-- Projects Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-layer-group mr-3 text-blue-600 dark:text-blue-400"></i>
                        Projects Overview
                    </h2>
                </div>

                @if($projectStats->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Project Name</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Tasks</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Progress</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Task Breakdown</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($projectStats as $stat)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer" wire:click="selectProject({{ $stat['id'] }})">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                            <div class="flex items-center">
                                                <div class="ml-3">
                                                    <p class="text-gray-900 dark:text-white font-semibold">{{ $stat['name'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-center">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white">
                                                {{ $stat['total_tasks'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center justify-center">
                                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ $stat['progress_percentage'] }}%"></div>
                                                </div>
                                                <span class="ml-2 text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $stat['progress_percentage'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center justify-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                    <i class="fas fa-spinner text-xs mr-1"></i> {{ $stat['in_progress_tasks'] }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                    <i class="fas fa-check text-xs mr-1"></i> {{ $stat['completed_tasks'] }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer" wire:click="selectProject(0)">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                            <div class="flex items-center">
                                                <div class="ml-3">
                                                    <p class="text-gray-900 dark:text-white font-semibold">All Task</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-center">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white">
                                                
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center justify-center">
                                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-300"></div>
                                                </div>
                                                <span class="ml-2 text-xs font-semibold text-gray-700 dark:text-gray-300"> %</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center justify-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                    <i class="fas fa-spinner text-xs mr-1"></i>  
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                    <i class="fas fa-check text-xs mr-1"></i>  
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">No projects yet. Create your first project to get started!</p>
                        <a href="{{ route('project') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-2"></i> Create Project
                        </a>
                    </div>
                @endif
            </div>

            <!-- Selected Project Tasks Details -->
            @if($selectedProject && $projectStats->isNotEmpty())
                @php
                    $selectedStat = $projectStats->firstWhere('id', $selectedProject);
                @endphp
                @if($selectedStat)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden animate-fadeIn">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700/50 dark:to-gray-700/50 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-tasks mr-3 text-blue-600 dark:text-blue-400"></i>
                                Task Details for <span class="ml-2 text-blue-600 dark:text-blue-400">{{ $selectedStat['name'] }}</span>
                            </h2>
                            <button wire:click="clearSelection()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                <div wire:click="loadProjectTasks({{ $selectedProject }}, 'all')" class="bg-gradient-to-br from-gray-50 to-gray-100 hover:cursor-pointer dark:from-gray-700/50 dark:to-gray-700 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Tasks</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $selectedStat['total_tasks'] }}</p>
                                </div>
                                <div wire:click="loadProjectTasks({{ $selectedProject }}, 'complete')" class="bg-gradient-to-br from-green-50 to-green-100 hover:cursor-pointer dark:from-green-900/30 dark:to-green-900/20 rounded-lg p-4">
                                    <p class="text-sm text-green-700 dark:text-green-300">Completed</p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $selectedStat['completed_tasks'] }}</p>
                                </div>
                                <div wire:click="loadProjectTasks({{ $selectedProject }}, 'progress')" class="bg-gradient-to-br from-yellow-50 to-yellow-100 hover:cursor-pointer dark:from-yellow-900/30 dark:to-yellow-900/20 rounded-lg p-4">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">In Progress</p>
                                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $selectedStat['in_progress_tasks'] }}</p>
                                </div>
                                <div wire:click="loadProjectTasks({{ $selectedProject }}, 'pending')" class="bg-gradient-to-br from-blue-50 to-blue-100 hover:cursor-pointer dark:from-blue-900/30 dark:to-blue-900/20 rounded-lg p-4">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">Pending</p>
                                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $selectedStat['pending_tasks'] }}</p>
                                </div>
                            </div>

                            <!-- Active Tasks List -->
                            @if(count($selectedProjectTasks) > 0)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <i class="fas fa-list mr-2 text-blue-600 dark:text-blue-400"></i>
                                        Active Tasks ({{ count($selectedProjectTasks) }})
                                    </h3>
                                    <div class="space-y-3 max-h-96 overflow-y-auto">
                                        @foreach($selectedProjectTasks as $task)
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-l-4 
                                                @if($task['status'] == 'progress') border-yellow-500 
                                                @else border-blue-500 @endif">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $task['title'] }}</h4>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                                                @if($task['status'] == 'progress') 
                                                                    bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                                                @else 
                                                                    bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                                                @endif">
                                                                @if($task['status'] == 'progress')
                                                                    <i class="fas fa-spinner text-xs mr-1"></i> In Progress
                                                                @else
                                                                    <i class="fas fa-clock text-xs mr-1"></i> <span>{{ ucfirst($task['status']) }}</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-gray-400 justify-between">
                                                            <span><i class="fas fa-user mr-1"></i>{{ $task['owner_name'] }}</span>
                                                            @if($task['target_date'])
                                                                <span><i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($task['target_date'])->format('M d, Y') }}</span>
                                                            @endif
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                                                @if(strtolower($task['priority']) == 'high') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                                                @elseif(strtolower($task['priority']) == 'medium') bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300
                                                                @else bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                                                @endif">
                                                                {{ ucfirst($task['priority'] ?? 'medium') }}
                                                            </span>
                                                            @if ($task['status'] !== 'complete')
                                                                <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" wire:change="setStatus({{ $task['id'] }}, $event.target.value)" value="{{ $task['id'] }}">                                                            
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-8 text-center">
                                    <i class="fas fa-check-circle text-4xl text-green-400 dark:text-green-600 mb-2 opacity-50"></i>
                                    <p class="text-gray-600 dark:text-gray-400">No active tasks. All tasks are completed! 🎉</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-in-out;
    }
</style>
