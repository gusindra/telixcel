# Dashboard UI - New Layout Visual Guide

## 📱 Updated Dashboard Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│  Dashboard                               Last updated: Just now  📅  │
│  Welcome back! Here's your project overview.  [Monthly View Button] │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────────┬──────────────────┬──────────────────┬────────────┐
│ Total Projects   │  Total Tasks     │  In Progress     │ Completed  │
│       5          │       47         │       12         │     28     │
└──────────────────┴──────────────────┴──────────────────┴────────────┘

┌──────────────────┬──────────────────┬──────────────────┐
│ Pending Tasks    │ In Progress Ts.  │ Completed Tasks  │
│       7          │       12         │        28        │
└──────────────────┴──────────────────┴──────────────────┘

Projects Overview
┌─────────────────┬─────────┬──────┬───────────┬───────────────┬─────┐
│ Project Name    │ Status  │Tasks │ Progress  │ Task Breakdown│ Act │
├─────────────────┼─────────┼──────┼───────────┼───────────────┼─────┤
│ 📁 Project 1    │ Active ✓│  8   │ ████░░ 75%│ 6✓ 2▶        │ →   │
└─────────────────┴─────────┴──────┴───────────┴───────────────┴─────┘

[Click Project to Expand]

┌─────────────────────────────────────────────────────────────────────┐
│ 📋 Task Details for "Project 1"                                   ✕ │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│ ┌────────────────┬─────────────────┬──────────────┬──────────────┐  │
│ │ Total Tasks: 8 │ Completed: 6    │ In Progress: 2│ Pending: 0 │  │
│ └────────────────┴─────────────────┴──────────────┴──────────────┘  │
│                                                                       │
│ Progress Overview                                                     │
│ Overall Completion: ████████░░░░ 75%                                 │
│                                                                       │
│ ═══════════════════════════════════════════════════════════════════ │
│                                                                       │
│ 📋 Active Tasks (2)          ← NEW FEATURE                           │
│                                                                       │
│ ┌──────────────────────────────────────────────────────────────┐   │
│ │ ▶ Fix login form validation      [In Progress] [High]       │   │
│ │   Assigned to: John Doe          Target: Jun 15, 2026       │   │
│ └──────────────────────────────────────────────────────────────┘   │
│                                                                       │
│ ┌──────────────────────────────────────────────────────────────┐   │
│ │ ⏳ Update dashboard styling         [Pending] [Medium]       │   │
│ │   Assigned to: Jane Smith         Target: Jun 20, 2026       │   │
│ └──────────────────────────────────────────────────────────────┘   │
│                                                                       │
│ (Scrollable list with max height)                                    │
│                                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 New Features Breakdown

### 1. Monthly View Button

```
┌─────────────────────────────────────────────────────────┐
│ Dashboard          Last updated: Just now    📅 Monthly │
│                                            [   View   ]  │
│ Welcome back! Here's your project overview.             │
└─────────────────────────────────────────────────────────┘

Button Details:
├─ Icon: 📅 (calendar-alt)
├─ Label: Monthly View
├─ Color: Blue (#3B82F6)
├─ Style: Rounded with shadow
└─ Position: Top-right header
```

### 2. Active Tasks Section

```
📋 Active Tasks (2)
│
├─ Task 1
│  ├─ Title: Fix login form validation
│  ├─ Status: In Progress (Yellow Badge)
│  ├─ Priority: High (Red Badge)
│  ├─ Owner: John Doe
│  └─ Target: Jun 15, 2026
│
├─ Task 2
│  ├─ Title: Update dashboard styling
│  ├─ Status: Pending (Blue Badge)
│  ├─ Priority: Medium (Orange Badge)
│  ├─ Owner: Jane Smith
│  └─ Target: Jun 20, 2026
│
└─ Features:
   ├─ Scrollable (max 396px)
   ├─ Color-coded status
   ├─ Color-coded priority
   └─ Dark mode support
```

---

## 🎨 Color Coding Reference

### Status Badges
```
In Progress:
├─ Background: 🟨 Yellow-100
├─ Text: 🟨 Yellow-800
├─ Dark Mode: 🟨 Yellow-900/30
└─ Icon: ▶️ Spinner

Pending:
├─ Background: 🟦 Blue-100
├─ Text: 🟦 Blue-800
├─ Dark Mode: 🟦 Blue-900/30
└─ Icon: ⏳ Clock
```

### Priority Badges
```
High Priority:
├─ Background: 🟥 Red-100
├─ Text: 🟥 Red-800
└─ Dark Mode: 🟥 Red-900/30

Medium Priority:
├─ Background: 🟧 Orange-100
├─ Text: 🟧 Orange-800
└─ Dark Mode: 🟧 Orange-900/30

Low Priority:
├─ Background: 🟩 Green-100
├─ Text: 🟩 Green-800
└─ Dark Mode: 🟩 Green-900/30
```

---

## 📲 Responsive Views

### Mobile View (< 768px)
```
┌─────────────────────────┐
│ Dashboard               │
│ Welcome back!           │
│                         │
│ [Monthly View Button]   │
│ (may wrap to next line) │
└─────────────────────────┘

Stats Cards:
┌─────────────────────────┐
│ Total Projects: 5       │
└─────────────────────────┘
┌─────────────────────────┐
│ Total Tasks: 47         │
└─────────────────────────┘
(etc. - one per row)

Projects Table:
(scrollable horizontally)

Task Details:
┌─────────────────────────┐
│ Task Details            │
│                         │
│ Total Tasks: 8          │
│ Completed: 6            │
│ In Progress: 2          │
│ Pending: 0              │
│                         │
│ Progress Bar:           │
│ ████████░░░░ 75%       │
│                         │
│ Active Tasks:           │
│ [Task 1]                │
│ [Task 2]                │
│ (scrollable)            │
└─────────────────────────┘
```

### Tablet View (768px - 1024px)
```
┌──────────────────────────────────────────┐
│ Dashboard  Last updated: Just now        │
│ [Monthly View Button]                    │
│ Welcome back!                            │
└──────────────────────────────────────────┘

Stats Cards (2 columns):
┌──────────────────┬──────────────────┐
│ Total Projects   │ Total Tasks      │
└──────────────────┴──────────────────┘
┌──────────────────┬──────────────────┐
│ In Progress      │ Completed        │
└──────────────────┴──────────────────┘

Task Details (full width):
Active Tasks:
┌──────────────────────────────────────┐
│ Task 1 [In Progress] [High]          │
│ Owner: John Doe | Target: Jun 15    │
├──────────────────────────────────────┤
│ Task 2 [Pending] [Medium]            │
│ Owner: Jane Smith | Target: Jun 20  │
└──────────────────────────────────────┘
```

### Desktop View (> 1024px)
```
┌──────────────────────────────────────────────────────────┐
│ Dashboard                    Last updated: Just now      │
│ Welcome back!         [📅 Monthly View Button]          │
└──────────────────────────────────────────────────────────┘

Stats Cards (4 columns):
┌─────────┬─────────┬─────────┬─────────┐
│ Total   │ Total   │In        │ Comp    │
│Projects │ Tasks   │Progress  │eted     │
└─────────┴─────────┴─────────┴─────────┘

Projects Table (full width):
┌──────────────────────────────────────┐
│ Project │Status │ Tasks │Progress │  │
└──────────────────────────────────────┘

Task Details (full width):
┌──────────────────────────────────────┐
│ Active Tasks (2)                     │
│                                      │
│ ┌────────────────────────────────┐ │
│ │ Task 1 | In Progress | High    │ │
│ │ John Doe | Jun 15, 2026        │ │
│ └────────────────────────────────┘ │
│ ┌────────────────────────────────┐ │
│ │ Task 2 | Pending | Medium      │ │
│ │ Jane Smith | Jun 20, 2026      │ │
│ └────────────────────────────────┘ │
│                                      │
│ (scrollable area)                    │
└──────────────────────────────────────┘
```

---

## 🔄 User Interaction Flow

```
1. User Opens Dashboard
   ↓
2. User sees:
   • Statistics cards
   • Task status overview
   • Projects table
   • Monthly View button
   ↓
3. User clicks "Monthly View" button
   ↓
   [Calendar view opens]
   ↓
4. User clicks project row
   ↓
5. Task Details section expands
   ↓
6. User sees:
   • Task counts (Total, Completed, In Progress, Pending)
   • Progress bar
   • Progress percentage
   • Active Tasks list (NEW)
   ↓
7. User views:
   • Task title with status badge
   • Owner name
   • Target date
   • Priority badge
   ↓
8. User can:
   • Scroll through task list
   • Click close button to collapse
   • Click project row again to select different project
```

---

## ⚙️ Task List Properties

### Displayed Information
```
For each active task:
├─ Title: Task name/title
├─ Status: In Progress or Pending (with badge)
├─ Priority: High, Medium, or Low (with badge)
├─ Owner: Task owner's name
├─ Target Date: Due date (formatted: M dd, YYYY)
└─ Border: Color-coded left border
   ├─ Yellow for In Progress
   └─ Blue for Pending
```

### Sorting Order
```
1. Primary: Status (descending)
   ├─ In Progress (first)
   └─ Pending (second)

2. Secondary: Created Date (descending)
   └─ Newest tasks first
```

### Display Behavior
```
If tasks exist:
  ├─ Show task list with scrollbar
  ├─ Max height: 396px (scrollable)
  └─ Each task in separate card

If no tasks exist:
  ├─ Show empty state
  ├─ Icon: ✓ Check circle (success)
  ├─ Message: "No active tasks. All tasks are completed! 🎉"
  └─ Styling: Green color, centered
```

---

## 🎯 Monthly View Button Behavior

### Current State
```
Button displays:
├─ Icon: 📅 Calendar-alt
├─ Text: "Monthly View"
├─ Link: "#" (placeholder)
└─ Status: Ready for integration
```

### Future Integration Options
```
Option 1: Link to calendar page
  └─ Route: /dashboard/calendar or similar

Option 2: Modal popup
  └─ Livewire component modal

Option 3: Inline calendar
  └─ Expand calendar below header

Option 4: External library integration
  ├─ Fullcalendar
  ├─ Litepicker
  └─ Other calendar libraries
```

---

## 📝 Empty State Example

```
When all tasks are completed:

┌──────────────────────────────────────┐
│              ✓✓✓                     │
│                                      │
│ No active tasks.                     │
│ All tasks are completed! 🎉          │
│                                      │
└──────────────────────────────────────┘

Styling:
├─ Icon: Large check circle (4xl)
├─ Icon color: Green-400 with 50% opacity
├─ Text: Gray-600 (dark: gray-400)
├─ Background: Light gray
└─ Padding: Generous spacing (8 vertical units)
```

---

This visual guide shows how the new dashboard layout appears with the calendar button and active tasks list!
