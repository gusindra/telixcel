# Dashboard Redesign Documentation

## Overview
The dashboard has been redesigned with a modern Livewire-based UI that showcases projects and tasks with real-time task counts and progress tracking.

## Features

### 1. **Statistics Dashboard**
- **Total Projects**: Shows the count of all projects in the current team
- **Total Tasks**: Displays the total number of tasks across all projects
- **In Progress**: Shows the count of tasks currently being worked on
- **Completed**: Displays the count of finished tasks

### 2. **Task Status Overview**
Three quick-view cards showing:
- **Pending Tasks**: Tasks waiting to be started
- **In Progress Tasks**: Tasks currently being worked on
- **Completed Tasks**: Finished tasks

### 3. **Projects Table**
A comprehensive table displaying:
- **Project Name**: The name of each project with an icon
- **Status**: Active, Completed, or Pending status
- **Total Tasks**: Count of all tasks in the project
- **Progress Bar**: Visual representation of completion percentage
- **Task Breakdown**: Quick stats showing In Progress and Completed counts
- **Action Button**: Link to view detailed project information

### 4. **Selected Project Details**
When clicking on a project row, an expanded view shows:
- Total tasks in the project
- Completed tasks count
- In Progress tasks count
- Pending tasks count
- Overall completion percentage with visual progress bar

## Technical Stack

### Backend
- **Framework**: Laravel 9.x
- **Component**: Livewire 2.x
- **Model Relations**: 
  - Project → Tasks (One-to-Many)
  - Team → Projects (One-to-Many)
  - Task → Owner (Many-to-One)

### Frontend
- **Styling**: Tailwind CSS
- **Icons**: Font Awesome
- **Responsive Design**: Mobile-first approach with TailwindCSS breakpoints

## File Structure

```
app/
├── Http/
│   └── Livewire/
│       └── Dashboard/
│           └── DashboardOverview.php      # Main Livewire component

resources/
├── views/
│   ├── dashboard.blade.php                # Main dashboard view
│   └── livewire/
│       └── dashboard/
│           └── dashboard-overview.blade.php   # Dashboard UI template

routes/
└── web.php                                # Route definitions
```

## Component Methods

### DashboardOverview.php

#### `mount()`
Initializes the component and loads the dashboard data when the page first loads.

#### `loadDashboardData()`
Fetches and calculates all dashboard statistics:
- Retrieves all projects for the current team
- Calculates total tasks, in-progress tasks, and completed tasks
- Builds project statistics with progress percentages
- Groups tasks by status

#### `selectProject($projectId)`
Updates the `selectedProject` property to display detailed information for a specific project.

#### `clearSelection()`
Resets the `selectedProject` to null, clearing the detailed view.

## Data Calculations

### Progress Percentage
```
progress_percentage = (completed_tasks / total_tasks) × 100
```

### Task Breakdown
- **Pending**: Tasks with status = 'pending'
- **In Progress**: Tasks with status = 'in_progress'
- **Completed**: Tasks with status = 'completed'

## Database Queries

The component uses the following queries:

```php
// Get all projects for the team with their tasks
Project::where('team_id', $teamId)
    ->with(['tasks'])
    ->get();

// Get all tasks for the projects
Task::whereIn('project_id', $this->projects->pluck('id'))->get();
```

## Styling Details

### Color Scheme
- **Blue**: Projects and primary actions
- **Purple**: Total tasks indicator
- **Yellow**: In-progress/active tasks
- **Green**: Completed tasks
- **Gray**: Pending/inactive items

### Responsive Breakpoints
- **Mobile**: 1 column
- **Tablet (md)**: 2 columns for stats, single column for tables
- **Desktop (lg)**: 4 columns for stats, 3 columns for task status overview

### Dark Mode Support
All components include full dark mode support with appropriate color variations for:
- Background colors
- Text colors
- Border colors
- Icon colors

## Usage

### Accessing the Dashboard
Navigate to `/dashboard` route when authenticated. The dashboard requires:
1. User authentication (middleware: `auth:sanctum`)
2. Email verification (middleware: `verified`)
3. Active team assignment

### Interacting with the Dashboard

1. **View Statistics**: All stats are automatically calculated and displayed
2. **Explore Projects**: Scroll through the projects table to see all projects
3. **View Project Details**: Click on any project row to expand and see detailed task breakdown
4. **Close Details**: Click the X button to close the expanded project view

## Customization Options

### Modifying Task Status Colors
Edit the `$colors` array in the Blade template to change the color scheme for different task statuses.

### Adding New Statistics
Extend the `loadDashboardData()` method in the DashboardOverview component to calculate new metrics.

### Changing Table Columns
Modify the table `<thead>` and `<tbody>` sections in the Blade template to add or remove columns.

## Performance Considerations

1. **Query Optimization**: Uses `with()` to eager load task relationships
2. **Collection Methods**: Uses Laravel collections for efficient data filtering
3. **Caching**: Consider adding query caching for large datasets:
   ```php
   $this->projects = Project::where('team_id', $teamId)
       ->with(['tasks'])
       ->remember(60) // Cache for 60 seconds
       ->get();
   ```

## Future Enhancements

1. **Filters**: Add filters for project status, date range, or team member
2. **Exports**: Add CSV/PDF export functionality for reports
3. **Charts**: Integrate Livewire Charts for visual data representation
4. **Real-time Updates**: Add Livewire polling for real-time task updates
5. **Search**: Implement project/task search functionality
6. **Sorting**: Add column sorting in the projects table
7. **Pagination**: Implement pagination for large project lists
8. **Task Creation**: Add inline task creation from the dashboard

## Troubleshooting

### Dashboard not loading
- Ensure user is authenticated and has verified email
- Check that user has an active team assigned
- Verify PHP version meets minimum requirements (8.0.2+)

### Data not updating
- Clear Laravel cache: `php artisan cache:clear`
- Clear view cache: `php artisan view:clear`
- Verify database connections

### Styling issues
- Ensure Tailwind CSS is properly compiled
- Run: `npm run dev` or `npm run prod`
- Clear browser cache

## Related Models

### Project Model
```php
public function tasks()
{
    return $this->hasMany('App\Models\Task', 'project_id');
}

public function team()
{
    return $this->belongsTo('App\Models\Team');
}
```

### Task Model
```php
public function project()
{
    return $this->belongsTo(Project::class, 'project_id');
}

public function owner()
{
    return $this->belongsTo(User::class, 'owner_id');
}
```

## Support & Issues
For issues or feature requests related to the dashboard, please refer to the main project documentation.
