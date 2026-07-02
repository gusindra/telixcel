<?php

namespace Tests\Feature;

use App\Http\Livewire\Ticket\Board;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketBoardTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        foreach (['TASK', 'TICKET', 'PROJECT', 'SETTING'] as $m) {
            foreach (['VIEW', 'CREATE', 'UPDATE', 'DELETE', 'AUDIT'] as $a) {
                \App\Models\Permission::updateOrCreate(['name' => "{$a} {$m}"], ['model' => $m]);
            }
        }
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'admin', 'description' => 'Admin']);
        foreach (\App\Models\Permission::all() as $perm) {
            \App\Models\PermissionRole::updateOrCreate(['role_id' => $role->id, 'permission_id' => $perm->id]);
        }
        $user = User::create(['name' => 'Admin', 'email' => uniqid('a') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        RoleUser::create(['user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1, 'status' => 'active', 'active' => 1, 'working_id' => 'A']);
        $this->actingAs($user->fresh());
        return $user;
    }

    // ──────────────────── CREATE TICKET ────────────────────

    /** @test */
    public function it_creates_a_ticket_with_reasons(): void
    {
        $this->actingAsAdmin();

        Livewire::test(Board::class)
            ->set('reasons', 'Server down')
            ->set('priority', 'high')
            ->call('createTicket')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tickets', [
            'reasons' => 'Server down',
            'priority' => 'high',
            'status' => 'open',
        ]);
    }

    /** @test */
    public function create_ticket_requires_reasons(): void
    {
        $this->actingAsAdmin();

        Livewire::test(Board::class)
            ->set('reasons', '')
            ->call('createTicket')
            ->assertHasErrors(['reasons']);
    }

    /** @test */
    public function create_ticket_resets_form_after_success(): void
    {
        $this->actingAsAdmin();

        Livewire::test(Board::class)
            ->set('reasons', 'Bug report')
            ->set('priority', 'low')
            ->call('createTicket')
            ->assertSet('reasons', '')
            ->assertSet('priority', 'medium');
    }

    // ──────────────────── STATUS LIFECYCLE ────────────────────

    /** @test */
    public function set_status_moves_through_lifecycle(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)->call('setStatus', $ticket->id, 'in_progress');
        $this->assertSame('in_progress', $ticket->fresh()->status);

        Livewire::test(Board::class)->call('setStatus', $ticket->id, 'resolved');
        $this->assertSame('resolved', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->resolved_at);

        Livewire::test(Board::class)->call('setStatus', $ticket->id, 'closed');
        $this->assertSame('closed', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->closed_at);
    }

    /** @test */
    public function set_status_ignores_invalid_status(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)->call('setStatus', $ticket->id, 'bogus');
        $this->assertSame('open', $ticket->fresh()->status);
    }

    /** @test */
    public function set_status_ignores_same_status(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)->call('setStatus', $ticket->id, 'open');
        $this->assertSame('open', $ticket->fresh()->status);
    }

    // ──────────────────── PRIORITY ────────────────────

    /** @test */
    public function set_priority_updates_ticket_priority(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'low', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)->call('setPriority', $ticket->id, 'high');
        $this->assertSame('high', $ticket->fresh()->priority);
    }

    /** @test */
    public function set_priority_ignores_invalid_value(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'low', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)->call('setPriority', $ticket->id, 'urgent');
        $this->assertSame('low', $ticket->fresh()->priority);
    }

    // ──────────────────── ASSIGN ────────────────────

    /** @test */
    public function assign_links_ticket_to_a_role(): void
    {
        $this->actingAsAdmin();
        $role = Role::create(['name' => 'Operasional', 'type' => 'operasional', 'role_for' => 'team', 'description' => 'Ops']);
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)->call('assign', $ticket->id, $role->id);
        $this->assertSame($role->id, $ticket->fresh()->role_id);
    }

    /** @test */
    public function assign_null_unassigns_ticket(): void
    {
        $this->actingAsAdmin();
        $role = Role::create(['name' => 'Finance', 'type' => 'finance', 'role_for' => 'team', 'description' => 'Fin']);
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id(), 'role_id' => $role->id]);

        Livewire::test(Board::class)->call('assign', $ticket->id, 0);
        $this->assertNull($ticket->fresh()->role_id);
    }

    // ──────────────────── TODO FROM TICKET ────────────────────

    /** @test */
    public function create_todo_from_ticket_creates_task_linked_to_ticket(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Fix login bug', 'status' => 'open', 'priority' => 'high', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)
            ->call('openTodo', $ticket->id)
            ->set('todoTitle', 'Fix login')
            ->set('todoType', 'admin')
            ->set('todoTarget', now()->addDays(3)->toDateString())
            ->call('createTodo')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Fix login',
            'ticket_id' => $ticket->id,
            'status' => 'pending',
            'priority' => 'high',
        ]);
    }

    /** @test */
    public function open_todo_prefills_title_from_ticket_reasons(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Password reset not working', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)
            ->call('openTodo', $ticket->id)
            ->assertSet('todoTitle', 'Password reset not working')
            ->assertSet('showTodoModal', true);
    }

    /** @test */
    public function create_todo_validates_required_fields(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)
            ->call('openTodo', $ticket->id)
            ->set('todoTitle', '')
            ->set('todoType', '')
            ->set('todoTarget', '')
            ->call('createTodo')
            ->assertHasErrors(['todoTitle', 'todoType', 'todoTarget']);
    }

    // ──────────────────── LINK / UNLINK TASK ────────────────────

    /** @test */
    public function link_task_attaches_task_to_ticket(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);
        $task = Task::create(['project_id' => null, 'title' => 'Existing Task', 'type' => 'admin', 'status' => 'pending', 'team_id' => 1, 'owner_id' => 1, 'target_date' => now()]);

        Livewire::test(Board::class)
            ->call('openLink', $ticket->id)
            ->set('linkTaskId', $task->id)
            ->call('linkTask')
            ->assertHasNoErrors();

        $this->assertSame($ticket->id, (int) $task->fresh()->ticket_id);
    }

    /** @test */
    public function link_task_validates_task_exists(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)
            ->call('openLink', $ticket->id)
            ->set('linkTaskId', 99999)
            ->call('linkTask')
            ->assertHasErrors(['linkTaskId']);
    }

    /** @test */
    public function unlink_task_detaches_task_from_ticket(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);
        $task = Task::create(['project_id' => null, 'title' => 'Linked Task', 'type' => 'admin', 'status' => 'pending', 'team_id' => 1, 'owner_id' => 1, 'target_date' => now(), 'ticket_id' => $ticket->id]);

        Livewire::test(Board::class)->call('unlinkTask', $task->id);

        $this->assertNull($task->fresh()->ticket_id);
    }

    // ──────────────────── DELETE ────────────────────

    /** @test */
    public function confirm_delete_shows_modal(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'Test', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);

        Livewire::test(Board::class)
            ->call('confirmDelete', $ticket->id)
            ->assertSet('confirmingDelete', true)
            ->assertSet('deleteId', $ticket->id);
    }

    /** @test */
    public function delete_ticket_removes_it_and_de_links_tasks(): void
    {
        $this->actingAsAdmin();
        $ticket = Ticket::create(['reasons' => 'To Delete', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) auth()->id()]);
        $task = Task::create(['project_id' => null, 'title' => 'Attached', 'type' => 'admin', 'status' => 'pending', 'team_id' => 1, 'owner_id' => auth()->id(), 'target_date' => now(), 'ticket_id' => $ticket->id]);

        Livewire::test(Board::class)
            ->call('confirmDelete', $ticket->id)
            ->call('deleteTicket');

        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
        $this->assertNull($task->fresh()->ticket_id);
    }

    // ──────────────────── VISIBILITY ────────────────────

    /** @test */
    public function non_manager_sees_only_own_or_assigned_tickets(): void
    {
        $role = Role::create(['name' => 'Ops', 'type' => 'operasional', 'role_for' => 'team', 'description' => 'Ops']);
        $user = User::create(['name' => 'OpsUser', 'email' => uniqid('o') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        RoleUser::create(['user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1, 'status' => 'active', 'active' => 1, 'working_id' => 'O']);
        $this->actingAs($user->fresh());

        Ticket::create(['reasons' => 'My ticket', 'status' => 'open', 'priority' => 'medium', 'created_by' => (string) $user->id]);
        Ticket::create(['reasons' => 'Other ticket', 'status' => 'open', 'priority' => 'medium', 'created_by' => '999']);

        $component = Livewire::test(Board::class)->instance();
        $ref = new \ReflectionMethod($component, 'render');
        $ref->setAccessible(true);
        $data = $ref->invoke($component);

        $titles = $data['tickets']->pluck('reasons')->all();
        $this->assertContains('My ticket', $titles);
        $this->assertNotContains('Other ticket', $titles);
    }
}
