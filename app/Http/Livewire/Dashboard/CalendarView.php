<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Livewire\Component;

class CalendarView extends Component
{
    public $currentMonth;
    public $currentYear;
    public $selectedMonth;
    public $selectedYear;
    public $tasks = [];
    public $calendarDays = [];
    public $monthTasks = [];

    public function mount()
    {
        $now = Carbon::now();
        $this->currentMonth = $now->month;
        $this->currentYear = $now->year;
        $this->selectedMonth = $now->month;
        $this->selectedYear = $now->year;

        $this->generateCalendar();
        $this->loadTasksForMonth();
    }

    public function previousMonth()
    {
        $this->selectedMonth--;
        if ($this->selectedMonth < 1) {
            $this->selectedMonth = 12;
            $this->selectedYear--;
        }
        $this->generateCalendar();
        $this->loadTasksForMonth();
    }

    public function nextMonth()
    {
        $this->selectedMonth++;
        if ($this->selectedMonth > 12) {
            $this->selectedMonth = 1;
            $this->selectedYear++;
        }
        $this->generateCalendar();
        $this->loadTasksForMonth();
    }

    public function jumpToMonth($month, $year)
    {
        $this->selectedMonth = $month;
        $this->selectedYear = $year;
        $this->generateCalendar();
        $this->loadTasksForMonth();
    }

    public function jumpToToday()
    {
        $now = Carbon::now();
        $this->selectedMonth = $now->month;
        $this->selectedYear = $now->year;
        $this->generateCalendar();
        $this->loadTasksForMonth();
    }

    private function generateCalendar()
    {
        $firstDay = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

        // Get the day of week for the first day (0 = Sunday, 1 = Monday, etc.)
        $startDay = $firstDay->dayOfWeek;

        // Calculate number of days to show from previous month
        $daysFromPrevMonth = $startDay;

        // Initialize calendar days
        $this->calendarDays = [];

        // Add days from previous month
        if ($daysFromPrevMonth > 0) {
            $prevMonth = $firstDay->copy()->subMonth();
            $prevLastDay = $prevMonth->daysInMonth;
            for ($i = $daysFromPrevMonth - 1; $i >= 0; $i--) {
                $this->calendarDays[] = [
                    'day' => $prevLastDay - $i,
                    'date' => $prevMonth->copy()->setDay($prevLastDay - $i),
                    'currentMonth' => false,
                    'isToday' => false,
                ];
            }
        }

        // Add days from current month
        for ($day = 1; $day <= $lastDay->day; $day++) {
            $date = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, $day);
            $isToday = $date->isToday();

            $this->calendarDays[] = [
                'day' => $day,
                'date' => $date,
                'currentMonth' => true,
                'isToday' => $isToday,
            ];
        }

        // Add days from next month to fill the grid
        $remainingDays = 42 - count($this->calendarDays); // 6 rows × 7 days
        $nextMonth = $lastDay->copy()->addDay();
        for ($day = 1; $day <= $remainingDays; $day++) {
            $date = $nextMonth->copy()->setDay($day);
            $this->calendarDays[] = [
                'day' => $day,
                'date' => $date,
                'currentMonth' => false,
                'isToday' => false,
            ];
        }
    }

    private function loadTasksForMonth()
    {
        $team = auth()->user()->currentTeam;

        if (!$team) {
            $this->monthTasks = [];
            return;
        }

        // Get all tasks for the current team in the selected month
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        /*if(auth()->user()->activeRole && str_contains(auth()->user()->activeRole->role->name, "Super Admin")){
            $tasks = Task::orderBy('target_date');
        }else{
            // Get all projects for the team
            $project = Project::whereHas('members', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                })
                ->pluck('id');
            $tasks = Task::orderBy('target_date')
                ->whereIn('project_id', $project);
        }
        $tasks = $tasks->whereNotNull('target_date')*/
        //dd(my_task_types());
        $tasks = Task::where('team_id', $team->id)
            ->forMyType()
            ->whereNotNull('target_date')
            ->whereBetween('target_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with(['project', 'owner'])
            ->orderBy('created_at', 'desc')
            ->get();
        if($tasks->count()==0){ 
            $project = Project::whereHas('members', function ($query) {
                        $query->where('user_id', auth()->user()->id);
                    })
                    ->with(['tasks'])
                    ->get();
            $tasks = Task::whereIn('project_id', $project->pluck('id'))
                    ->forMyType()
                    ->with(['project', 'owner', 'assignedTo', 'children' => fn ($q) => $q->orderBy('created_at')])
                    ->orderBy('created_at')
                    ->get();
        }

        // Group tasks by date
        $this->monthTasks = [];
        foreach ($tasks as $task) {
            $dateKey = $task->target_date->format('Y-m-d');
            if (!isset($this->monthTasks[$dateKey])) {
                $this->monthTasks[$dateKey] = [];
            }
            $this->monthTasks[$dateKey][] = [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority ?? 'medium',
                'project_name' => $task->project?->name ?? 'Unknown Project',
                'owner_name' => $task->owner?->name ?? 'Unassigned',
            ];
        }
    }

    public function getTasksForDate($dateString)
    {
        return $this->monthTasks[$dateString] ?? [];
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'pending' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'complete' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    public function getStatusLabel($status)
    {
        return match ($status) {
            'progress' => '▶',
            'pending' => '⏳',
            'complete' => '✓',
            default => '•',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.calendar-view');
    }
}
