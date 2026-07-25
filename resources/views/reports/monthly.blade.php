<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>{{ $label }}</title>
<style>
@page { margin: 1.5cm; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #1a1a1a; }
h1 { font-size: 15pt; text-align: center; margin-bottom: 4pt; }
.subtitle { text-align: center; font-size: 9pt; color: #666; margin-bottom: 16pt; }
h2 { font-size: 12pt; border-bottom: 1px solid #ccc; padding-bottom: 4pt; margin-top: 16pt; }
table { width: 100%; border-collapse: collapse; margin: 8pt 0 10pt; font-size: 8.5pt; }
th { background: #f0f0f0; text-align: left; padding: 5pt 6pt; border: 1px solid #ddd; }
td { padding: 4pt 6pt; border: 1px solid #ddd; }
.summary-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 8pt; margin: 10pt 0; }
.user-section { margin-top: 12pt; }
.user-section h3 { font-size: 10.5pt; margin-bottom: 3pt; color: #333; }
</style></head>
<body>
<h1>{{ $label }}</h1>
<p class="subtitle">Generated: {{ now()->format('d M Y H:i') }} — Telixcel</p>
<div class="summary-box">
<p><strong>Total Completed:</strong> {{ $data['summary']['total_completed'] ?? 0 }}</p>
<p><strong>Total Pending:</strong> {{ $data['summary']['total_pending'] ?? 0 }}</p>
@if (!empty($data['summary']['total_users']))<p><strong>Users Active:</strong> {{ $data['summary']['total_users'] }}</p>@endif
</div>
@if ($data['type'] === 'admin' && !empty($data['per_user']))
<h2>Completed Tasks per User</h2>
@foreach ($data['per_user'] as $group)
<div class="user-section"><h3>{{ $group['user_name'] }} ({{ $group['task_count'] }} tasks)</h3>
<table><thead><tr><th>ID</th><th>Task</th><th>Project</th><th>Type</th><th>Priority</th><th>Completed</th></tr></thead><tbody>
@foreach ($group['tasks'] as $t)
<tr><td>#{{ $t['id'] }}</td><td>{{ $t['title'] }}</td><td>{{ $t['project'] }}</td><td>{{ $t['type'] }}</td><td>{{ $t['priority'] }}</td><td>{{ $t['completed_at'] }}</td></tr>
@endforeach
</tbody></table></div>
@endforeach
@if (!empty($data['pending']))
<h2>Pending Tasks (All Users)</h2>
<table><thead><tr><th>ID</th><th>Task</th><th>Project</th><th>Type</th><th>Status</th><th>Priority</th><th>Assigned</th><th>Target</th></tr></thead><tbody>
@foreach ($data['pending'] as $t)
<tr><td>#{{ $t['id'] }}</td><td>{{ $t['title'] }}</td><td>{{ $t['project'] }}</td><td>{{ $t['type'] }}</td><td>{{ $t['status'] }}</td><td>{{ $t['priority'] }}</td><td>{{ $t['assigned_to'] }}</td><td>{{ $t['target_date'] ?? '—' }}</td></tr>
@endforeach
</tbody></table>
@endif
@endif
@if ($data['type'] === 'user')
<h2>Completed Tasks</h2>
@if (empty($data['completed']))<p>No completed tasks this month.</p>
@else
<table><thead><tr><th>ID</th><th>Task</th><th>Project</th><th>Type</th><th>Priority</th><th>Completed</th></tr></thead><tbody>
@foreach ($data['completed'] as $t)
<tr><td>#{{ $t['id'] }}</td><td>{{ $t['title'] }}</td><td>{{ $t['project'] }}</td><td>{{ $t['type'] }}</td><td>{{ $t['priority'] }}</td><td>{{ $t['completed_at'] }}</td></tr>
@endforeach
</tbody></table>
@endif
<h2>Pending Tasks (Your Type)</h2>
@if (empty($data['pending']))<p>No pending tasks.</p>
@else
<table><thead><tr><th>ID</th><th>Task</th><th>Project</th><th>Type</th><th>Status</th><th>Priority</th><th>Target</th></tr></thead><tbody>
@foreach ($data['pending'] as $t)
<tr><td>#{{ $t['id'] }}</td><td>{{ $t['title'] }}</td><td>{{ $t['project'] }}</td><td>{{ $t['type'] }}</td><td>{{ $t['status'] }}</td><td>{{ $t['priority'] }}</td><td>{{ $t['target_date'] ?? '—' }}</td></tr>
@endforeach
</tbody></table>
@endif
@endif
</body></html>
