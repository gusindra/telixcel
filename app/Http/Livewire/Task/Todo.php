<?php

namespace App\Http\Livewire\Task;

use App\Models\LogChange;
use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;

class Todo extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $project_id;

    /** When set (e.g. user detail page), show only this user's own/assigned tasks. */
    public $ownerId = null;

    // form fields
    public $title;
    public $type;
    public $priority = 'medium';
    public $source;
    public $target_date;
    public $parent_id = 0;   // 0 = root task, else parent task id
    public $assigned_to = null; // user the task is assigned to (optional)

    public $showForm = false;

    // delete confirmation
    public $confirmingDelete = false;
    public $deleteId = null;
    public $deleteTitle = '';
    public $deleteIsParent = false;

    // status-comment modal (for terminal statuses that require a reason)
    public $commentModal = false;
    public $commentTaskId = null;
    public $commentStatus = null;
    public $statusComment = '';

    public const TYPES = ['finance', 'admin', 'operasional'];
    public const PRIORITIES = ['low', 'medium', 'high'];

    /** All valid task statuses. */
    public const STATUSES = ['progress', 'pending', 'complete', 'declined', 'cancelled', 'aborted'];

    /** Statuses that require a mandatory comment/reason before they can be applied. */
    public const COMMENT_REQUIRED = ['declined', 'cancelled', 'aborted'];

    /** "Closed" statuses sink to the bottom of the list. */
    public const CLOSED = ['complete', 'declined', 'cancelled', 'aborted'];

    public function mount($id = null, $ownerId = null)
    {
        $this->project_id = $id;
        $this->ownerId = $ownerId;
        $this->resetForm();
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string',
            'source' => 'nullable|string',
            'target_date' => 'required|date',
            'parent_id' => 'nullable',
        ];
        // Type is chosen only for root tasks; sub-tasks inherit it from the parent.
        if (! $this->parent_id) {
            $rules['type'] = 'required|in:' . implode(',', self::TYPES);
        }
        $rules['priority'] = 'required|in:' . implode(',', self::PRIORITIES);
        return $rules;
    }

    public function create()
    {
        $this->validate();

        // Sub-task inherits its Type from the parent (best practice: consistent role scope).
        $type = $this->type;
        if ($this->parent_id) {
            $type = optional(Task::find($this->parent_id))->type ?? $this->type;
        }

        Task::create([
            'project_id'  => $this->project_id,
            'parent_id'   => $this->parent_id ?: 0,
            'title'       => $this->title,
            'type'        => $type,
            'priority'    => $this->priority,
            'source'      => $this->source,
            'target_date' => $this->target_date,
            'owner_id'    => auth()->id(),
            'assigned_to' => $this->assigned_to ?: null,
            'team_id'     => auth()->user()->current_team_id,
            'status'      => 'pending',
        ]);

        $this->resetForm();
        $this->showForm = false;
        $this->emit('task_saved');
    }

    /** Open the create modal, optionally pre-selecting a parent (for "+ Sub-task"). */
    public function actionShowModal($parentId = 0)
    {
        $this->resetForm();
        $this->parent_id = $parentId;
        $this->showForm = true;
    }

    public function setStatus($taskId, $status)
    {
        // Terminal statuses must go through the comment modal (defense in depth:
        // even if the front-end routes here directly).
        if (in_array($status, self::COMMENT_REQUIRED, true)) {
            $this->requestStatusComment($taskId, $status);
            return;
        }
        if (! in_array($status, self::STATUSES, true)) {
            return;
        }
        $task = Task::find($taskId);
        if ($task && $this->canManage($task)) {
            $from = $task->status;
            if ($from === $status) {
                return;
            }
            $before = $task->toJson();
            // Moving back to a non-terminal status clears any previous reason.
            $task->update(['status' => $status, 'status_note' => null]);
            $this->logStatus($task, $from, $status, $before);
            $this->syncParentStatus($task);
        }
    }

    /** Open the comment modal for a status that requires a mandatory reason. */
    public function requestStatusComment($taskId, $status)
    {
        if (! in_array($status, self::COMMENT_REQUIRED, true)) {
            return;
        }
        $task = Task::find($taskId);
        if (! $task || ! $this->canManage($task)) {
            return;
        }
        $this->commentTaskId = $taskId;
        $this->commentStatus = $status;
        $this->statusComment = '';
        $this->resetErrorBag('statusComment');
        $this->commentModal = true;
    }

    /** Apply a terminal status together with its mandatory comment/reason. */
    public function confirmStatusComment()
    {
        $this->validate([
            'statusComment' => 'required|string|min:3',
        ], [
            'statusComment.required' => __('A reason is required for this status.'),
            'statusComment.min'      => __('Please write at least 3 characters.'),
        ]);

        if (! in_array($this->commentStatus, self::COMMENT_REQUIRED, true)) {
            $this->commentModal = false;
            return;
        }

        $task = Task::find($this->commentTaskId);
        if ($task && $this->canManage($task)) {
            $from = $task->status;
            $before = $task->toJson();
            $task->update([
                'status'      => $this->commentStatus,
                'status_note' => $this->statusComment,
            ]);
            $this->logStatus($task, $from, $this->commentStatus, $before, $this->statusComment);
            $this->syncParentStatus($task);
        }

        $this->commentModal = false;
        $this->commentTaskId = null;
        $this->commentStatus = null;
        $this->statusComment = '';
    }

    /** Record a status change (who + from->to, plus reason) into LogChange for auditing. */
    private function logStatus(Task $task, $from, $to, $before, $comment = null): void
    {
        try {
            $remark = 'Status ' . $from . ' -> ' . $to
                . ' by ' . (auth()->user()->name ?? 'system');
            if ($comment) {
                $remark .= ' | Reason: ' . $comment;
            }
            LogChange::create([
                'model'    => 'Task',
                'model_id' => $task->id,
                'before'   => $before,
                'remark'   => $remark,
            ]);
        } catch (\Throwable $e) {
            // Auditing must never block the status update.
        }
    }

    /**
     * After a sub-task changes, sync the parent recursively up the chain:
     * - all children complete  -> parent complete
     * - any child not complete  -> parent back to progress (if it was complete)
     * Recurses until there is no parent (root task reached).
     */
    private function syncParentStatus(Task $task): void
    {
        if (! $task->parent_id) {
            return;
        }

        $parent = Task::find($task->parent_id);
        if (! $parent) {
            return;
        }

        $children = Task::where('parent_id', $parent->id)->get();
        if ($children->isEmpty()) {
            return;
        }

        $allComplete = $children->every(fn ($c) => $c->status === 'complete');

        if ($allComplete && $parent->status !== 'complete') {
            $parent->update(['status' => 'complete']);
            $this->syncParentStatus($parent); // propagate up
        } elseif (! $allComplete && $parent->status === 'complete') {
            $parent->update(['status' => 'progress']);
            $this->syncParentStatus($parent); // propagate up
        }
    }

    /** Open the delete confirmation modal. */
    public function confirmDelete($taskId)
    {
        $task = Task::find($taskId);
        if (! $task || ! $this->canManage($task)) {
            return;
        }
        $this->deleteId = $task->id;
        $this->deleteTitle = $task->title;
        // Flag if this task has any children (at any depth level)
        $this->deleteIsParent = Task::where('parent_id', $task->id)->exists();
        $this->confirmingDelete = true;
    }

    /** Actually delete after confirmation. */
    public function deleteTask()
    {
        $task = Task::find($this->deleteId);
        if ($task && $this->canManage($task)) {
            // Move children up one level (to task's parent, not always root)
            // This preserves hierarchy: sub-sub-tasks become sub-tasks of grandparent
            $newParentId = $task->parent_id ?: 0;
            Task::where('parent_id', $task->id)->update(['parent_id' => $newParentId]);
            $task->delete();
        }
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }

    private function resetForm()
    {
        $this->title = '';
        $this->type = '';
        $this->priority = 'medium';
        $this->source = '';
        $this->parent_id = 0;
        $this->assigned_to = null;
        $this->target_date = now()->addDays(7)->toDateString();
    }

    /**
     * Base query of every task the current view may see (team + project + role-type
     * or owner scope). A task is "visible" if it matches this — at ANY depth, so a
     * finance sub-task under an operasional parent still counts for a finance role.
     */
    private function scopedQuery()
    {
        $q = Task::query()->where('team_id', auth()->user()->current_team_id);

        if ($this->project_id) {
            $q->where('project_id', $this->project_id);
        } elseif (! $this->ownerId) {
            // Dashboard / all-tasks view: only projects the user is invited to.
            $invited = my_invited_project_ids();
            if ($invited !== null) {
                $q->whereIn('project_id', $invited);
            }
        }

        if ($this->ownerId) {
            // Per-user view (user detail page): that user's own/assigned tasks, all types.
            $q->where(function ($w) {
                $w->where('owner_id', $this->ownerId)
                  ->orWhere('assigned_to', $this->ownerId);
            });
        } else {
            // Visibility: Super Admin → all; else own/assigned + role-type matches.
            // (Assigned tasks must show even when type differs from active role.)
            $q->forMyType();
        }

        return $q;
    }

    /** Visible tasks for this view, returned as a tree (unlimited depth). */
    private function tree()
    {
        // All task ids the user may see (any level). A visible task becomes a display
        // "root" when its parent is NOT visible (e.g. a finance sub-task whose parent
        // is operasional), so matching tasks are never hidden behind a filtered parent.
        $visibleIds = $this->scopedQuery()->pluck('id')->all();

        $roots = $this->scopedQuery()
            ->with(['owner', 'assignedTo'])
            ->where(function ($q) use ($visibleIds) {
                $q->where('parent_id', 0);
                if (! empty($visibleIds)) {
                    $q->orWhereNotIn('parent_id', $visibleIds);
                }
            })
            ->orderByRaw("status IN ('complete','declined','cancelled','aborted')")
            ->orderByRaw("FIELD(priority,'high','medium','low')")
            ->orderByRaw("FIELD(status,'progress','pending','complete','declined','cancelled','aborted')")
            ->orderBy('created_at')
            ->paginate(8, ['*'], 'todoPage');

        // Sort comparator: open first, then closed; oldest first within each rank.
        $rank = ['progress' => 0, 'pending' => 1, 'complete' => 2, 'declined' => 3, 'cancelled' => 4, 'aborted' => 5];
        $sorter = fn ($c) => $c->sortBy([
            fn ($a, $b) => ($rank[$a->status] ?? 1) <=> ($rank[$b->status] ?? 1),
            fn ($a, $b) => $a->created_at <=> $b->created_at,
        ])->values();

        // Only VISIBLE tasks are nested as children (same scope as the roots).
        $childMap = $this->scopedQuery()->with(['owner', 'assignedTo'])->get()->groupBy('parent_id');

        $roots->getCollection()->each(function ($root) use ($childMap, $sorter) {
            $this->attachChildNodes($root, $childMap, $sorter);
        });

        return $roots;
    }

    /**
     * Recursively attach childNodes relation to a task and all its descendants
     * from the pre-loaded flat $childMap (keyed by parent_id).
     */
    private function attachChildNodes($task, \Illuminate\Support\Collection $childMap, callable $sorter): void
    {
        $children = $sorter($childMap->get($task->id, collect()));
        $task->setRelation('childNodes', $children);

        foreach ($children as $child) {
            $this->attachChildNodes($child, $childMap, $sorter);
        }
    }

    /** Tasks eligible to be a parent (root tasks of this project). */
    private function parentOptions()
    {
        return Task::where('project_id', $this->project_id)
            ->where('parent_id', 0)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    /**
     * Task types the current user may see (roles.type). Delegates to the shared
     * my_task_types() helper so the rule is identical across the app.
     */
    private function myTypes(): array
    {
        return my_task_types();
    }

    private function canManage(Task $task): bool
    {
        // Managers, task owners, and assignees can update status / delete.
        if (
            $this->isManager()
            || (int) $task->owner_id === (int) auth()->id()
            || (int) $task->assigned_to === (int) auth()->id()
        ) {
            return true;
        }

        return in_array($task->type, $this->myTypes(), true);
    }

    private function isManager(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->super->first()?->role === 'superadmin') {
            return true;
        }
        return $user->activeRole && str_contains($user->activeRole->role->name ?? '', 'Admin');
    }

    /** Users a task may be assigned to: project members + owner (project tasks), else the team. */
    private function assignableUsers()
    {
        if ($this->project_id) {
            $project = \App\Models\Project::find($this->project_id);
            if ($project) {
                $ids = $project->members()->pluck('users.id')
                    ->push($project->user_id)->push(auth()->id())
                    ->filter()->unique()->all();

                return \App\Models\User::whereIn('id', $ids)->orderBy('name')->get();
            }
        }
        $team = auth()->user()->currentTeam;

        return $team ? $team->allUsers() : collect([auth()->user()]);
    }

    public function render()
    {
        return view('livewire.task.todo', [
            'types'      => self::TYPES,
            'priorities' => self::PRIORITIES,
            'roots'      => $this->tree(),
            'parents'    => $this->parentOptions(),
            'assignableUsers' => $this->assignableUsers(),
            'dashboard'  => ! $this->project_id,
        ]);
    }
}
