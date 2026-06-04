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

    // form fields
    public $title;
    public $type;
    public $priority = 'medium';
    public $source;
    public $target_date;
    public $parent_id = 0;   // 0 = root task, else parent task id

    public $showForm = false;

    // delete confirmation
    public $confirmingDelete = false;
    public $deleteId = null;
    public $deleteTitle = '';
    public $deleteIsParent = false;

    public const TYPES = ['finance', 'admin', 'operasional'];
    public const PRIORITIES = ['low', 'medium', 'high'];

    private const ROLE_TYPE_MAP = [
        'Accounting' => 'finance', 'Commercial' => 'finance',
        'Operational' => 'operasional', 'Project Manager' => 'operasional',
        'Agent' => 'operasional', 'Admin' => 'admin',
    ];

    public function mount($id = null)
    {
        $this->project_id = $id;
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
        if (! in_array($status, ['progress', 'pending', 'complete'], true)) {
            return;
        }
        $task = Task::find($taskId);
        if ($task && $this->canManage($task)) {
            $from = $task->status;
            if ($from === $status) {
                return;
            }
            $before = $task->toJson();
            $task->update(['status' => $status]);
            $this->logStatus($task, $from, $status, $before);
            $this->syncParentStatus($task);
        }
    }

    /** Record a status change (who + from->to) into LogChange for auditing. */
    private function logStatus(Task $task, $from, $to, $before): void
    {
        try {
            LogChange::create([
                'model'    => 'Task',
                'model_id' => $task->id,
                'before'   => $before,
                'remark'   => 'Status ' . $from . ' -> ' . $to
                    . ' by ' . (auth()->user()->name ?? 'system'),
            ]);
        } catch (\Throwable $e) {
            // Auditing must never block the status update.
        }
    }

    /**
     * After a sub-task changes, sync the parent:
     * - all children complete  -> parent complete
     * - any child not complete  -> parent back to progress (if it was complete)
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
        } elseif (! $allComplete && $parent->status === 'complete') {
            $parent->update(['status' => 'progress']);
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
        $this->deleteIsParent = $task->parent_id == 0 && Task::where('parent_id', $task->id)->exists();
        $this->confirmingDelete = true;
    }

    /** Actually delete after confirmation. */
    public function deleteTask()
    {
        $task = Task::find($this->deleteId);
        if ($task && $this->canManage($task)) {
            // children become root tasks so they are not orphaned/hidden
            Task::where('parent_id', $task->id)->update(['parent_id' => 0]);
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
        $this->target_date = now()->addDays(7)->toDateString();
    }

    /** Visible tasks for this project, scoped by role, returned as a tree. */
    private function tree()
    {
        // Root tasks only, paginated. Project page -> single project; dashboard -> all visible.
        $query = Task::query()->with('owner')->where('parent_id', 0);

        if ($this->project_id) {
            $query->where('project_id', $this->project_id);
        }

        if ($this->isManager()) {
            $query->where('team_id', auth()->user()->current_team_id);
        } else {
            $uid = auth()->id();
            $types = $this->myTypes();
            $query->where(function ($q) use ($uid, $types) {
                $q->where('owner_id', $uid);
                if (! empty($types)) {
                    $q->orWhereIn('type', $types);
                }
            });
        }

        // progress & pending first, complete last; oldest first within each.
        // completed sink to the bottom; then HIGH priority pinned to top; then status; then oldest.
        $query->orderByRaw("status = 'complete'")
              ->orderByRaw("FIELD(priority,'high','medium','low')")
              ->orderByRaw("FIELD(status,'progress','pending','complete')")
              ->orderBy('created_at');

        $roots = $query->paginate(8, ['*'], 'todoPage');

        // attach sorted children for the roots on this page
        $rank = ['progress' => 0, 'pending' => 1, 'complete' => 2];
        $sorter = fn ($c) => $c->sortBy([
            fn ($a, $b) => ($rank[$a->status] ?? 1) <=> ($rank[$b->status] ?? 1),
            fn ($a, $b) => $a->created_at <=> $b->created_at,
        ])->values();

        $childMap = Task::whereIn('parent_id', $roots->pluck('id') ?: [0])->get()->groupBy('parent_id');

        $roots->getCollection()->transform(function ($root) use ($childMap, $sorter) {
            $root->setRelation('childNodes', $sorter($childMap->get($root->id, collect())));
            return $root;
        });

        return $roots;
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
     * Task types the current user may see, taken from roles.type (data-driven,
     * set by TaskRoleSeeder). Falls back to the name map if type is empty.
     */
    private function myTypes(): array
    {
        $user = auth()->user();
        if (! $user) {
            return [];
        }
        $types = [];
        foreach ($user->role as $roleUser) {
            $role = $roleUser->role;
            if (! $role) {
                continue;
            }
            if (! empty($role->type)) {
                $types[] = $role->type;
                continue;
            }
            // fallback for roles whose type hasn't been seeded yet
            foreach (self::ROLE_TYPE_MAP as $needle => $type) {
                if (str_contains($role->name ?? '', $needle)) {
                    $types[] = $type;
                }
            }
        }
        return array_values(array_unique($types));
    }

    private function canManage(Task $task): bool
    {
        if ($this->isManager() || $task->owner_id === auth()->id()) {
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

    public function render()
    {
        return view('livewire.task.todo', [
            'types'      => self::TYPES,
            'priorities' => self::PRIORITIES,
            'roots'      => $this->tree(),
            'parents'    => $this->parentOptions(),
            'dashboard'  => ! $this->project_id,
        ]);
    }
}
