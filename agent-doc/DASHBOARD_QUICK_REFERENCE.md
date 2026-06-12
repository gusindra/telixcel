# Dashboard - Quick Reference Guide

## 📱 Access
**URL**: `http://your-app.local/dashboard`
**Requirements**: Authenticated user + verified email + active team

## 🎯 Main Features at a Glance

| Feature | Location | Description |
|---------|----------|-------------|
| **Statistics Cards** | Top section | Shows Total Projects, Total Tasks, In Progress, Completed |
| **Status Overview** | Below stats | Three quick-view boxes for Pending/In Progress/Completed |
| **Projects Table** | Main section | Lists all projects with task counts and progress |
| **Project Details** | Click row | Expands to show detailed task breakdown |

## 📊 Components Used

### DashboardOverview (Main)
```
Location: app/Http/Livewire/Dashboard/DashboardOverview.php
View: resources/views/livewire/dashboard/dashboard-overview.blade.php
Route: GET /dashboard
```

### ProjectTasksTable (Optional)
```
Location: app/Http/Livewire/Dashboard/ProjectTasksTable.php
View: resources/views/livewire/dashboard/project-tasks-table.blade.php
Usage: @livewire('dashboard.project-tasks-table')
```

## 💾 Database Status Values

Task statuses used in the system:
- `pending` - Not started
- `progress` - In progress
- `complete` - Finished

## 🎨 Color Guide

```
Blue     → Projects, Primary Actions
Yellow   → In Progress Tasks
Green    → Completed Tasks
Gray     → Pending/Inactive
Purple   → Total Metrics
```

## ⚡ Quick Customization

### Change the Dashboard Title
File: `resources/views/livewire/dashboard/dashboard-overview.blade.php`
```html
<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
```

### Add New Statistic Card
File: `app/Http/Livewire/Dashboard/DashboardOverview.php`
```php
public function loadDashboardData()
{
    // ... existing code ...
    $this->yourNewMetric = Task::where('priority', 'high')->count();
}
```

### Adjust Progress Bar Colors
File: `resources/views/livewire/dashboard/dashboard-overview.blade.php`
```html
<div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full"></div>
```

## 🔗 Related Routes

| Route | Controller | Purpose |
|-------|-----------|---------|
| `/dashboard` | - | Main dashboard (Livewire) |
| `/project` | ProjectController | List all projects |
| `/project/{id}` | ProjectController | View project details |

## 📋 Task Status Calculations

```php
// Completed %
progress_percentage = (completed_tasks / total_tasks) × 100

// Task counts
pending = tasks where status = 'pending'
in_progress = tasks where status = 'progress'
completed = tasks where status = 'complete'
```

## 🛠️ Common Tasks

### Find Total Tasks Across All Projects
```php
$total = Task::whereIn('project_id', $projectIds)->count();
```

### Get Tasks by Status
```php
$completed = Task::whereIn('project_id', $projectIds)
    ->where('status', 'complete')
    ->count();
```

### Get Project with Task Count
```php
$project = Project::with('tasks')
    ->withCount('tasks')
    ->find($id);
```

## 🚨 Common Issues

| Issue | Solution |
|-------|----------|
| Dashboard blank | Check if user has active team |
| Wrong task counts | Verify status values in database |
| Layout broken | Rebuild CSS: `npm run dev` |
| Slow loading | Check database query performance |

## 📚 Key Files

```
├── app/Http/Livewire/Dashboard/DashboardOverview.php
├── resources/views/livewire/dashboard/dashboard-overview.blade.php
├── resources/views/dashboard.blade.php
└── routes/web.php
```

## 🔐 Authentication Check

Dashboard requires these middleware:
- `auth:sanctum` - Must be logged in
- `verified` - Email must be verified
- Active `currentTeam` - Must be on a team

## 💡 Tips

1. **Dark Mode**: Full dark mode support with `dark:` prefix
2. **Responsive**: Works on mobile, tablet, and desktop
3. **Real-time**: Livewire provides reactive updates
4. **Performance**: Uses eager loading for relationships
5. **Accessibility**: Includes ARIA labels and semantic HTML

## 📞 Need Help?

1. Check DASHBOARD_README.md for detailed documentation
2. Review DASHBOARD_IMPLEMENTATION.md for technical details
3. Check Laravel Livewire docs: https://laravel-livewire.com/

---

**Version**: 1.0
**Last Updated**: June 10, 2026
