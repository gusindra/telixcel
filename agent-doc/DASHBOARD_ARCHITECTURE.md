# Dashboard Architecture & Visual Guide

## 📐 System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                          User Browser                            │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                    GET /dashboard (Authenticated)
                                │
┌───────────────────────────────▼─────────────────────────────────┐
│                      Laravel Router                              │
│  Route::get('/dashboard', DashboardController@show)             │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                    Check Authentication & Email
                    Check Active Team Assignment
                                │
┌───────────────────────────────▼─────────────────────────────────┐
│                    Dashboard Blade View                          │
│  resources/views/dashboard.blade.php                            │
│  @livewire('dashboard.dashboard-overview')                      │
└───────────────────────────────┬─────────────────────────────────┘
                                │
┌───────────────────────────────▼─────────────────────────────────┐
│              Livewire Component - DashboardOverview              │
│  app/Http/Livewire/Dashboard/DashboardOverview.php             │
│                                                                  │
│  Properties:                                                     │
│  - $projects: Array of projects                                │
│  - $totalTasks: Total task count                               │
│  - $tasksInProgress: In-progress task count                    │
│  - $tasksCompleted: Completed task count                       │
│  - $projectStats: Array of stats per project                  │
│  - $selectedProject: Currently selected project ID             │
│  - $tasksByStatus: Distribution by status                      │
│                                                                  │
│  Methods:                                                        │
│  - mount(): Initialize component                               │
│  - loadDashboardData(): Fetch and calculate stats             │
│  - selectProject($id): Show project details                   │
│  - clearSelection(): Hide project details                      │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                    Database Queries
                                │
        ┌───────────────────────┬───────────────────────┐
        │                       │                       │
        ▼                       ▼                       ▼
    ┌────────────┐         ┌────────────┐         ┌────────────┐
    │  Projects  │         │   Tasks    │         │   Teams    │
    │ (from DB)  │         │ (from DB)  │         │ (from DB)  │
    └────────────┘         └────────────┘         └────────────┘
                                │
                    Data Processing & Calculation
                                │
        ┌───────────────────────┬───────────────────────┐
        │                       │                       │
        ▼                       ▼                       ▼
   Statistics            Project Stats            Status Dist.
   ────────────         ──────────────           ────────────
   • Total Projects     • Project Name           • Pending
   • Total Tasks        • Task Count             • In Progress
   • In Progress        • Completed %            • Completed
   • Completed          • Task Breakdown
                        • Progress Bar
                                │
                    Render Blade Template
                                │
┌───────────────────────────────▼─────────────────────────────────┐
│         Dashboard UI - dashboard-overview.blade.php             │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐│
│  │ Statistics Cards (4 cards)                                 ││
│  │ • Total Projects │ Total Tasks │ In Progress │ Completed  ││
│  └────────────────────────────────────────────────────────────┘│
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐│
│  │ Task Status Overview (3 cards)                             ││
│  │ • Pending │ In Progress │ Completed                        ││
│  └────────────────────────────────────────────────────────────┘│
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐│
│  │ Projects Table (Interactive)                               ││
│  │ ┌──────────────────────────────────────────────────────┐  ││
│  │ │ Project │ Status │ Tasks │ Progress │ Breakdown │ Action │  ││
│  │ ├──────────────────────────────────────────────────────┤  ││
│  │ │ Project 1 │ Active │  8   │  75%     │ 6✓ 2▶    │  →    │  ││
│  │ │ Project 2 │ Active │ 12   │  50%     │ 6✓ 4▶    │  →    │  ││
│  │ │ Project 3 │ Pending│  5   │  20%     │ 1✓ 2▶    │  →    │  ││
│  │ └──────────────────────────────────────────────────────┘  ││
│  └────────────────────────────────────────────────────────────┘│
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐│
│  │ Project Details (Expandable)                               ││
│  │ [Shown when project is selected]                           ││
│  │                                                            ││
│  │ Total Tasks: 8  │ Completed: 6  │ In Progress: 2          ││
│  │ Progress: ████████░░░░ 75%                                 ││
│  └────────────────────────────────────────────────────────────┘│
└───────────────────────────────────────────────────────────────┘
                                │
                    HTML + CSS + JavaScript
                                │
┌───────────────────────────────▼─────────────────────────────────┐
│                    Rendered Web Page                             │
│              (Displayed in User's Browser)                       │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🎨 Dashboard Component Layout

```
┌─────────────────────────────────────────────────────────────────┐
│  Dashboard Header - "Dashboard"                                  │
│  Welcome back! Here's your project overview.                     │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────┬─────────────────┬─────────────────┬──────────┐
│ Total Projects  │  Total Tasks    │  In Progress    │ Completed│
│       5         │       47        │       12        │    28    │
└─────────────────┴─────────────────┴─────────────────┴──────────┘

┌─────────────────┬─────────────────┬─────────────────┐
│ Pending Tasks   │ In Progress Ts. │ Completed Tasks │
│       7         │       12        │       28        │
└─────────────────┴─────────────────┴─────────────────┘

Projects Overview
┌────────────────────────────────────────────────────────────────┐
│ Project Name  │ Status    │ Tasks │ Progress │ Breakdown │ Act│
├────────────────────────────────────────────────────────────────┤
│ 📁 Project 1  │ Active ✓  │  8    │ ██████░░ 75% │ 6✓ 2▶   │→ │
│ 📁 Project 2  │ Active ✓  │ 12    │ ████░░░░ 50% │ 6✓ 4▶   │→ │
│ 📁 Project 3  │ Completed │ 15    │ ████████ 100%│ 15✓ 0▶  │→ │
│ 📁 Project 4  │ Active ✓  │  6    │ ██░░░░░░ 33% │ 2✓ 3▶   │→ │
│ 📁 Project 5  │ Pending   │  5    │ ░░░░░░░░ 0%  │ 0✓ 1▶   │→ │
└────────────────────────────────────────────────────────────────┘

[Click any project to expand details]

Project Details - "Project 1" ✕
┌────────────────────────────────────────────────────────────────┐
│ Total Tasks: 8    │  Completed: 6  │  In Progress: 2  │ Pend: 0│
│                                                                  │
│ Overall Completion                                              │
│ ████████░░░░ 75%                                                │
└────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        User Interaction                          │
│                    (Navigate to /dashboard)                      │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│               DashboardOverview Component                        │
│                      mount() called                              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                  loadDashboardData()                             │
│                                                                  │
│  1. Get current team ID                                         │
│  2. Query: SELECT * FROM projects WHERE team_id = ?            │
│  3. Load related: with(['tasks'])                              │
│  4. Count total projects                                        │
│  5. Get all tasks from projects                                 │
│  6. Filter tasks by status (progress, pending, complete)       │
│  7. Calculate statistics                                        │
│  8. Build project stats array with calculations                │
│  9. Populate tasksByStatus array                               │
│  10. Trigger render                                             │
└────────────────────────┬────────────────────────────────────────┘
                         │
                ┌────────┼────────┐
                │        │        │
                ▼        ▼        ▼
        ┌──────────┐ ┌──────────┐ ┌──────────┐
        │ Projects │ │  Tasks   │ │Statistics│
        │ Array    │ │ Array    │ │ Object   │
        └──────────┘ └──────────┘ └──────────┘
                │        │        │
                └────────┼────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│              Blade Template Rendering                           │
│        dashboard-overview.blade.php                             │
│                                                                  │
│  Loops through data and renders:                                │
│  • Statistics cards                                             │
│  • Status overview boxes                                        │
│  • Projects table                                               │
│  • Expandable project details                                   │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    HTML/CSS/JavaScript                          │
│              (Sent to Browser & Rendered)                       │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│              User Sees Dashboard in Browser                      │
│                                                                  │
│  Can:                                                            │
│  • View statistics                                              │
│  • See all projects in table                                    │
│  • Click to expand project details                              │
│  • Navigate to project detail pages                             │
└─────────────────────────────────────────────────────────────────┘

[If User Clicks Project Row]
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│        Livewire AJAX Request - selectProject($id)               │
│                                                                  │
│  1. Update $selectedProject property                            │
│  2. Component re-renders with new data                          │
│  3. Project details appear with animation                       │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│           Dashboard Shows Expanded Project Details              │
│                                                                  │
│  Displays:                                                       │
│  • Project name in header                                       │
│  • Total tasks count                                            │
│  • Completed tasks count                                        │
│  • In progress tasks count                                      │
│  • Pending tasks count                                          │
│  • Overall completion percentage                                │
│  • Visual progress bar                                          │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📊 Database Query Sequence

```
1. User Visits Dashboard
   ↓
2. Laravel Route Handler
   → Check Authentication
   → Check Email Verification
   → Check Team Assignment
   ↓
3. DashboardOverview Component Initialization
   → mount()
   → loadDashboardData()
   ↓
4. Database Query 1: Get Projects
   SELECT * FROM projects WHERE team_id = ? WITH tasks
   ↓
   ┌─────────────────────────────────────────┐
   │ Result: Collection of Project objects   │
   │ - Project 1 (with 8 tasks)              │
   │ - Project 2 (with 12 tasks)             │
   │ - Project 3 (with 15 tasks)             │
   │ - Project 4 (with 6 tasks)              │
   │ - Project 5 (with 5 tasks)              │
   └─────────────────────────────────────────┘
   ↓
5. Count Projects
   $totalProjects = 5
   ↓
6. Database Query 2: Get All Tasks
   SELECT * FROM tasks WHERE project_id IN (1,2,3,4,5)
   ↓
   ┌─────────────────────────────────────────┐
   │ Result: Collection of Task objects      │
   │ Total: 46 tasks                         │
   │ - Status: progress → 12 tasks           │
   │ - Status: pending → 7 tasks             │
   │ - Status: complete → 27 tasks           │
   └─────────────────────────────────────────┘
   ↓
7. In-Memory Data Processing
   → Filter by status
   → Calculate percentages
   → Build statistics array
   → Create project stats
   ↓
8. Component Properties Updated
   • $totalProjects = 5
   • $totalTasks = 46
   • $tasksInProgress = 12
   • $tasksCompleted = 27
   • $projectStats = [...]
   • $tasksByStatus = [...]
   ↓
9. Render Blade Template with Data
   ↓
10. Return HTML to Browser
```

---

## 🎯 Component State Management

```
Initial State (on mount)
┌──────────────────────────────────────────┐
│ projects = []                            │
│ totalProjects = 0                        │
│ totalTasks = 0                           │
│ tasksInProgress = 0                      │
│ tasksCompleted = 0                       │
│ projectStats = []                        │
│ selectedProject = null                   │
│ tasksByStatus = []                       │
└──────────────────────────────────────────┘

After loadDashboardData()
┌──────────────────────────────────────────┐
│ projects = [Project1, Project2, ...]     │
│ totalProjects = 5                        │
│ totalTasks = 46                          │
│ tasksInProgress = 12                     │
│ tasksCompleted = 27                      │
│ projectStats = [                         │
│   {id:1, name:P1, total:8, ...},        │
│   {id:2, name:P2, total:12, ...},       │
│   ...                                    │
│ ]                                        │
│ selectedProject = null                   │
│ tasksByStatus = {                        │
│   pending: 7,                            │
│   in_progress: 12,                       │
│   completed: 27                          │
│ }                                        │
└──────────────────────────────────────────┘

After selectProject(2)
┌──────────────────────────────────────────┐
│ [All data from above, plus:]             │
│ selectedProject = 2                      │
│ [Blade renders expanded view]            │
└──────────────────────────────────────────┘

After clearSelection()
┌──────────────────────────────────────────┐
│ [All data from above, except:]           │
│ selectedProject = null                   │
│ [Expanded view is hidden]                │
└──────────────────────────────────────────┘
```

---

## 🎨 Responsive Design Breakpoints

```
Mobile (< 768px)
┌──────────────────────┐
│  Statistics (1 col)  │
├──────────────────────┤
│  Status Overview     │
│  (1 col stacked)     │
├──────────────────────┤
│  Projects Table      │
│  (Scrollable)        │
├──────────────────────┤
│  Project Details     │
│  (If selected)       │
└──────────────────────┘

Tablet (768px - 1024px)
┌────────────────┬────────────────┐
│ Statistics (2) │ Statistics (2)  │
├────────────────┼────────────────┤
│ Status Overview (3 cols)        │
├─────────────────────────────────┤
│ Projects Table (full width)      │
├─────────────────────────────────┤
│ Project Details (if selected)    │
└─────────────────────────────────┘

Desktop (> 1024px)
┌──────────┬──────────┬──────────┬──────────┐
│Stats(1)  │Stats(2)  │Stats(3)  │Stats(4)  │
├──────────┼──────────┼──────────┼──────────┤
│Status(1) │Status(2) │Status(3) │          │
├──────────┴──────────┴──────────┴──────────┤
│ Projects Table (full width, optimized)    │
├───────────────────────────────────────────┤
│ Project Details (if selected, full width) │
└───────────────────────────────────────────┘
```

---

## 🔌 Component Integration Points

```
Livewire Component
    │
    ├─→ Blade Template (View)
    │   └─→ HTML/CSS/JS
    │
    ├─→ Database (Models)
    │   ├─→ Project Model
    │   └─→ Task Model
    │
    ├─→ Laravel Route
    │   └─→ Authentication Middleware
    │
    └─→ JavaScript (Livewire Wire)
        └─→ AJAX Requests
            └─→ Component Updates
```

---

## 📱 Dark Mode Implementation

```
Light Mode (Default)
┌─────────────────────────────────────┐
│ Background: White                   │
│ Text: Dark Gray                     │
│ Borders: Light Gray                 │
│ Cards: White with shadows           │
│ Accents: Blue, Green, Yellow        │
└─────────────────────────────────────┘

Dark Mode (dark: prefix)
┌─────────────────────────────────────┐
│ dark:bg-gray-800 (Background)       │
│ dark:text-white (Text)              │
│ dark:border-gray-700 (Borders)      │
│ dark:bg-gray-700/50 (Cards)         │
│ dark:text-blue-400 (Accents)        │
└─────────────────────────────────────┘

Tailwind CSS Class Example:
bg-white dark:bg-gray-800      (Background)
text-gray-900 dark:text-white  (Text)
border-gray-200 dark:border-gray-700 (Borders)
```

---

## 🌐 URL & Route Map

```
Dashboard Routes:
├── /dashboard
│   └── GET → resources/views/dashboard.blade.php
│       └── @livewire('dashboard.dashboard-overview')
│
Project Routes (Linked from Dashboard):
├── /project
│   └── GET → List all projects
│
└── /project/{id}
    └── GET → View project details

Livewire Component Routes (AJAX):
├── /livewire/message
│   ├── POST → selectProject($id)
│   └── POST → clearSelection()
```

---

This visual guide provides a comprehensive understanding of how the dashboard system works, from user interaction to data rendering.
