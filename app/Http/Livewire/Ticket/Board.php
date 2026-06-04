<?php

namespace App\Http\Livewire\Ticket;

use App\Models\LogChange;
use App\Models\Role;
use App\Models\Task;
use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Standalone ticket board. A ticket (request/issue) can spawn or link to To-do
 * tasks (tasks.ticket_id). Status lifecycle: open -> in_progress -> resolved -> closed.
 * Every change is audited via LogChange. Visibility is role-scoped.
 */
class Board extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public const TYPES = ['finance', 'admin', 'operasional'];
    public const STATUSES = ['open', 'in_progress', 'resolved', 'closed'];
    public const PRIORITIES = ['low', 'medium', 'high'];

    // create ticket
    public $reasons;
    public $priority = 'medium';

    // to-do modal (create new)
    public $showTodoModal = false;
    public $todoTicketId = null;
    public $todoTitle = '';
    public $todoType = '';
    public $todoTarget;

    // link existing to-do modal
    public $showLinkModal = false;
    public $linkTicketId = null;
    public $linkTaskId = null;

    // delete confirmation
    public $confirmingDelete = false;
    public $deleteId = null;

    // ── tickets ──────────────────────────────────────────────────────────

    public function createTicket()
    {
        $this->validate([
            'reasons'  => 'required|string',
            'priority' => 'required|in:' . implode(',', self::PRIORITIES),
        ]);

        $ticket = Ticket::create([
            'reasons'    => $this->reasons,
            'status'     => 'open',
            'priority'   => $this->priority,
            'created_by' => (string) auth()->id(),
        ]);

        $this->logTicket($ticket, 'Ticket created');
        $this->reasons = '';
        $this->priority = 'medium';
        $this->resetValidation();
        $this->emit('saved');
    }

    public function setStatus($id, $status)
    {
        if (! in_array($status, self::STATUSES, true)) {
            return;
        }
        $ticket = Ticket::find($id);
        if (! $ticket || $ticket->status === $status) {
            return;
        }

        $before = $ticket->status;
        $ticket->status = $status;
        $ticket->updated_by = (string) auth()->id();
        $ticket->resolved_at = $status === 'resolved' ? ($ticket->resolved_at ?: now()) : ($status === 'closed' ? $ticket->resolved_at : null);
        $ticket->closed_at = $status === 'closed' ? ($ticket->closed_at ?: now()) : null;
        $ticket->save();

        $this->logTicket($ticket, 'Status ' . $before . ' -> ' . $status);
    }

    public function setPriority($id, $priority)
    {
        if (! in_array($priority, self::PRIORITIES, true)) {
            return;
        }
        $ticket = Ticket::find($id);
        if ($ticket && $ticket->priority !== $priority) {
            $before = $ticket->priority;
            $ticket->update(['priority' => $priority, 'updated_by' => (string) auth()->id()]);
            $this->logTicket($ticket, 'Priority ' . $before . ' -> ' . $priority);
        }
    }

    /** Assign the ticket to a role (not a single user). */
    public function assign($id, $roleId)
    {
        $ticket = Ticket::find($id);
        if (! $ticket) {
            return;
        }
        $rid = $roleId ?: null;
        $ticket->update(['role_id' => $rid, 'updated_by' => (string) auth()->id()]);
        $name = $rid ? optional(Role::find($rid))->name : 'unassigned';
        $this->logTicket($ticket, 'Assigned to role ' . $name);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteTicket()
    {
        $ticket = Ticket::find($this->deleteId);
        if ($ticket) {
            Task::where('ticket_id', $ticket->id)->update(['ticket_id' => null]);
            $this->logTicket($ticket, 'Ticket deleted');
            $ticket->delete();
        }
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }

    // ── to-do (new) ──────────────────────────────────────────────────────

    public function openTodo($ticketId)
    {
        $this->todoTicketId = $ticketId;
        $this->todoTitle = optional(Ticket::find($ticketId))->reasons ?? '';
        $this->todoType = '';
        $this->todoTarget = now()->addDays(7)->toDateString();
        $this->showTodoModal = true;
    }

    public function createTodo()
    {
        $this->validate([
            'todoTitle'  => 'required|string',
            'todoType'   => 'required|in:' . implode(',', self::TYPES),
            'todoTarget' => 'required|date',
        ]);

        Task::create([
            'project_id'  => null,
            'parent_id'   => 0,
            'ticket_id'   => $this->todoTicketId,
            'title'       => $this->todoTitle,
            'type'        => $this->todoType,
            'source'      => 'Ticket #' . $this->todoTicketId,
            'owner_id'    => auth()->id(),
            'team_id'     => auth()->user()->current_team_id,
            'status'      => 'pending',
            'target_date' => $this->todoTarget,
        ]);

        if ($ticket = Ticket::find($this->todoTicketId)) {
            $this->logTicket($ticket, 'To-do created: ' . $this->todoTitle);
        }

        $this->showTodoModal = false;
        $this->emit('saved');
    }

    // ── to-do (link existing) ────────────────────────────────────────────

    public function openLink($ticketId)
    {
        $this->linkTicketId = $ticketId;
        $this->linkTaskId = null;
        $this->showLinkModal = true;
    }

    public function linkTask()
    {
        $this->validate(['linkTaskId' => 'required|exists:tasks,id']);
        Task::where('id', $this->linkTaskId)->update(['ticket_id' => $this->linkTicketId]);

        if ($ticket = Ticket::find($this->linkTicketId)) {
            $this->logTicket($ticket, 'Linked to-do #' . $this->linkTaskId);
        }

        $this->showLinkModal = false;
        $this->emit('saved');
    }

    public function unlinkTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->ticket_id) {
            $ticketId = $task->ticket_id;
            $task->update(['ticket_id' => null]);
            if ($ticket = Ticket::find($ticketId)) {
                $this->logTicket($ticket, 'Unlinked to-do #' . $taskId);
            }
        }
    }

    // ── helpers ──────────────────────────────────────────────────────────

    private function logTicket(Ticket $ticket, string $remark): void
    {
        try {
            LogChange::create([
                'model'    => 'Ticket',
                'model_id' => $ticket->id,
                'before'   => $ticket->toJson(),
                'remark'   => $remark . ' by ' . (auth()->user()->name ?? 'system'),
            ]);
        } catch (\Throwable $e) {
            // auditing must never block the action
        }
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
        $query = Ticket::with(['tasks', 'role', 'createdBy'])
            ->orderByRaw("FIELD(status,'open','in_progress','resolved','closed')")
            ->orderByRaw("FIELD(priority,'high','medium','low')")
            ->orderBy('created_at', 'desc');

        // Role-based visibility: managers see all; others see tickets they created
        // or that are assigned to a role they hold.
        if (! $this->isManager()) {
            $uid = auth()->id();
            $myRoleIds = auth()->user()->role->map(fn ($r) => optional($r->role)->id)->filter()->all();
            $query->where(function ($q) use ($uid, $myRoleIds) {
                $q->where('created_by', (string) $uid);
                if (! empty($myRoleIds)) {
                    $q->orWhereIn('role_id', $myRoleIds);
                }
            });
        }

        $availableTasks = Task::whereNull('ticket_id')
            ->where('parent_id', 0)
            ->where('team_id', auth()->user()->current_team_id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'type']);

        return view('livewire.ticket.board', [
            'tickets'        => $query->paginate(10),
            'types'          => self::TYPES,
            'priorities'     => self::PRIORITIES,
            'availableTasks' => $availableTasks,
            'roles'          => Role::where('name', '!=', 'Super Admin')->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
