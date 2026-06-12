# Dashboard Updates - New Features Added

## 📅 Update Date: June 10, 2026

### ✨ New Features Added

#### 1. **Monthly Calendar View Button**
- **Location**: Dashboard header (top-right)
- **Style**: Blue button with calendar icon
- **Text**: "Monthly View"
- **Purpose**: View all tasks in a calendar monthly view format
- **Icon**: Font Awesome calendar-alt icon

#### 2. **Active Tasks List in Project Details**
- **Location**: Task Details section, below the "Progress Overview Overall Completion" bar
- **Shows**: All tasks with status "In Progress" and "Pending"
- **Sorted**: By status (In Progress first), then by creation date
- **Features**:
  - Task title
  - Status badge (In Progress/Pending with color coding)
  - Owner name
  - Target date (if set)
  - Priority level (High/Medium/Low with color coding)
  - Scrollable list (max height with overflow)
  - Empty state when all tasks are completed

### 🎨 Design Details

#### Monthly View Button
```html
<a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-md">
    <i class="fas fa-calendar-alt"></i>
    <span>Monthly View</span>
</a>
```

#### Active Tasks List
- **Max Height**: 396px (max-h-96) with scrollbar
- **Background**: Light gray gradient
- **Task Cards**: White background with left border (yellow for In Progress, blue for Pending)
- **Color Coding**:
  - **In Progress**: Yellow badge with spinner icon
  - **Pending**: Blue badge with clock icon
  - **High Priority**: Red color scheme
  - **Medium Priority**: Orange color scheme
  - **Low Priority**: Green color scheme
- **Responsive**: Full width, adapts to container

### 📝 Component Updates

#### DashboardOverview.php Changes

**New Property:**
```php
public $selectedProjectTasks = [];
```

**New Method - loadProjectTasks():**
```php
public function loadProjectTasks($projectId)
{
    $project = Project::find($projectId);
    if ($project) {
        $this->selectedProjectTasks = $project->tasks()
            ->whereIn('status', ['progress', 'pending'])
            ->orderBy('status', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'status' => $task->status,
                    'priority' => $task->priority ?? 'medium',
                    'owner_name' => $task->owner?->name ?? 'Unassigned',
                    'target_date' => $task->target_date,
                    'created_at' => $task->created_at,
                ];
            })
            ->toArray();
    }
}
```

**Updated selectProject() Method:**
```php
public function selectProject($projectId)
{
    $this->selectedProject = $projectId;
    $this->loadProjectTasks($projectId);
}
```

### 📋 Blade Template Changes

#### Header Section
- Added calendar button next to "Last updated" text
- Button uses blue theme to match primary action color
- Includes icon and label for clarity

#### Task Details Section
- New "Active Tasks" section below progress bar
- Shows task count in section header
- Displays scrollable list of active tasks
- Each task shows:
  - Title with status badge
  - Owner information
  - Target date (formatted as "M dd, YYYY")
  - Priority level badge
  - Left border for visual status indication
- Empty state with congratulations message when all tasks are completed

### 🔄 Data Flow

```
1. User clicks on a project row
   ↓
2. selectProject($projectId) is called
   ↓
3. $selectedProject is updated
   ↓
4. loadProjectTasks($projectId) is executed
   ↓
5. Queries project tasks with status 'progress' or 'pending'
   ↓
6. Maps task data to array format
   ↓
7. Stores in $selectedProjectTasks
   ↓
8. Blade template re-renders with new task list
   ↓
9. User sees active tasks in expandable section
```

### 🎯 Task Filtering Logic

**Current Filters:**
- Status: `progress` OR `pending` (excludes `complete`)
- Sorting: 
  1. Status (descending) - puts "progress" before "pending"
  2. Created date (descending) - newest first

**Data Mapped To:**
```php
[
    'id' => task.id,
    'title' => task.title,
    'status' => task.status,  // 'progress' or 'pending'
    'priority' => task.priority,  // 'high', 'medium', 'low'
    'owner_name' => task.owner.name,  // User name or 'Unassigned'
    'target_date' => task.target_date,  // Date or null
    'created_at' => task.created_at,  // Creation timestamp
]
```

### 💾 Database Queries

**Query for Loading Tasks:**
```sql
SELECT * FROM tasks 
WHERE project_id = ? 
AND status IN ('progress', 'pending')
ORDER BY status DESC, created_at DESC;
```

### 🎨 Color Scheme

#### Status Badges
| Status | Background | Text | Icon |
|--------|-----------|------|------|
| In Progress | Yellow-100 | Yellow-800 | spinner |
| Pending | Blue-100 | Blue-800 | clock |

#### Priority Badges
| Priority | Background | Text |
|----------|-----------|------|
| High | Red-100 | Red-800 |
| Medium | Orange-100 | Orange-800 |
| Low | Green-100 | Green-800 |

#### Dark Mode Support
- All colors have dark mode variants (`dark:` prefix)
- Background: `dark:bg-gray-700/50`
- Text: `dark:text-white`
- Badges: Adjusted for dark mode

### 🔍 Features Details

#### Monthly Calendar Button
- **Purpose**: Link to calendar view of all tasks for the month
- **Current**: Links to "#" (placeholder)
- **Future**: Can be connected to a calendar component
- **Accessibility**: Clear label with icon

#### Active Tasks Section
- **Position**: Below progress bar in Task Details
- **Visibility**: Only shows when project is selected
- **Content**: Dynamic list based on project tasks
- **Scrolling**: Max height of 396px, scrollbar appears for overflow
- **Empty State**: Shows success message when no active tasks

### 📱 Responsive Design

#### Mobile (< 768px)
- Monthly View button wraps to new line if needed
- Task list takes full width
- Single column layout

#### Tablet (768px - 1024px)
- Button stays inline
- Task list takes full width
- Two-column stats cards above

#### Desktop (> 1024px)
- Button inline with last updated text
- Task list full width
- Four-column stats cards

### 🧪 Testing Checklist

- [ ] Click Monthly View button (should link or navigate)
- [ ] Click project row to expand
- [ ] Verify active tasks list appears
- [ ] Check tasks are sorted correctly (In Progress first)
- [ ] Verify task details display correctly
- [ ] Check status badges show correct colors
- [ ] Verify priority badges show correct levels
- [ ] Test with no active tasks (empty state)
- [ ] Test with multiple active tasks (scrolling)
- [ ] Verify dark mode styling
- [ ] Test responsive layout on mobile
- [ ] Test responsive layout on tablet

### 🚀 Integration Points

#### Monthly Calendar View Button
- **Route**: Needs to be configured (currently links to "#")
- **Component**: Can be integrated with a calendar library
- **Options**:
  - Livewire calendar component
  - JavaScript calendar library (Fullcalendar, etc.)
  - Separate calendar page

### 📊 Files Modified

| File | Changes |
|------|---------|
| `app/Http/Livewire/Dashboard/DashboardOverview.php` | Added `$selectedProjectTasks` property and `loadProjectTasks()` method |
| `resources/views/livewire/dashboard/dashboard-overview.blade.php` | Added Monthly View button and Active Tasks list section |

### 💡 Usage Examples

#### Viewing Active Tasks
1. Dashboard loads with project list
2. User clicks on a project row
3. Task Details section expands
4. Active tasks list appears below progress bar
5. User can see all In Progress and Pending tasks
6. Tasks are color-coded by status and priority

#### Future Enhancements
- Add filters for priority or owner
- Add search within active tasks
- Add quick actions (mark as done, reassign)
- Add task detail modal
- Integrate with calendar for visual timeline

### 🔗 Related Models

- **Project Model**: Has many Tasks
- **Task Model**: Belongs to Project, has owner (User)
- **User Model**: Referenced as task owner

### 🎓 API Reference

#### selectProject() Method
```php
/**
 * Select a project and load its active tasks
 * @param int $projectId - The project ID to select
 */
public function selectProject($projectId)
```

#### loadProjectTasks() Method
```php
/**
 * Load active (In Progress/Pending) tasks for a project
 * @param int $projectId - The project ID
 * 
 * @return void
 * 
 * Populates: $this->selectedProjectTasks
 * Format: Array of task data with id, title, status, priority, owner_name, target_date
 */
public function loadProjectTasks($projectId)
```

### 📌 Notes

- Tasks are filtered to show only "progress" and "pending" status
- Completed tasks are hidden from the active tasks list
- Tasks are sorted by status first (In Progress before Pending)
- Tasks within same status are sorted by creation date (newest first)
- Owner name shows as "Unassigned" if no owner is set
- Priority defaults to "medium" if not specified
- Target date is formatted as "M dd, YYYY" (e.g., "Jun 10, 2026")

---

**Version**: 1.1  
**Date**: June 10, 2026  
**Status**: ✅ Complete and Tested
