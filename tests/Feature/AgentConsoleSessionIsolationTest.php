<?php

namespace Tests\Feature;

use App\Http\Livewire\AgentConsole;
use App\Models\AgentChat;
use App\Models\AgentChatMessage;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ensures AI Console chat sessions are strictly isolated per user.
 * User A must never load, list, write into, or delete User B's sessions.
 */
class AgentConsoleSessionIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $name = 'Admin'): User
    {
        $user = User::create([
            'name' => $name,
            'email' => uniqid(strtolower($name)) . '@test.com',
            'password' => bcrypt('password'),
            'current_team_id' => 1,
        ]);

        $role = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['type' => 'admin', 'role_for' => 'team', 'description' => 'Admin']
        );

        RoleUser::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'team_id' => 1,
            'status' => 'active',
            'active' => 1,
            'working_id' => 'A' . $user->id,
        ]);

        return $user->fresh();
    }

    /** @return array{0: User, 1: AgentChat, 2: User, 3: AgentChat} */
    private function twoUsersWithChats(): array
    {
        $userA = $this->makeAdmin('UserA');
        $userB = $this->makeAdmin('UserB');

        $chatA = AgentChat::create(['user_id' => $userA->id, 'title' => 'Secret chat of A']);
        AgentChatMessage::create([
            'agent_chat_id' => $chatA->id,
            'role' => 'user',
            'content' => 'pesan rahasia milik user A',
        ]);
        AgentChatMessage::create([
            'agent_chat_id' => $chatA->id,
            'role' => 'assistant',
            'content' => 'balasan rahasia untuk user A',
        ]);

        $chatB = AgentChat::create(['user_id' => $userB->id, 'title' => 'Secret chat of B']);
        AgentChatMessage::create([
            'agent_chat_id' => $chatB->id,
            'role' => 'user',
            'content' => 'pesan rahasia milik user B',
        ]);
        AgentChatMessage::create([
            'agent_chat_id' => $chatB->id,
            'role' => 'assistant',
            'content' => 'balasan rahasia untuk user B',
        ]);

        return [$userA, $chatA, $userB, $chatB];
    }

    /** @test */
    public function mount_restores_only_the_authenticated_users_latest_session(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();

        // B's chat is more recently updated, but A must not load it.
        $chatB->forceFill(['updated_at' => now()->addMinute()])->save();
        $chatA->forceFill(['updated_at' => now()->subMinute()])->save();

        $this->actingAs($userA);

        Livewire::test(AgentConsole::class)
            ->assertSet('chatId', $chatA->id)
            ->assertSee('balasan rahasia untuk user A')
            ->assertDontSee('balasan rahasia untuk user B')
            ->assertDontSee('pesan rahasia milik user B');
    }

    /** @test */
    public function history_sidebar_lists_only_own_sessions(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();
        $this->actingAs($userA);

        Livewire::test(AgentConsole::class)
            ->assertSee('Secret chat of A')
            ->assertDontSee('Secret chat of B');
    }

    /** @test */
    public function user_cannot_load_another_users_session(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();
        $this->actingAs($userA);

        Livewire::test(AgentConsole::class)
            ->call('loadChat', $chatB->id)
            // Still on A's own session (from mount), never switched to B.
            ->assertSet('chatId', $chatA->id)
            ->assertDontSee('balasan rahasia untuk user B')
            ->assertSee('balasan rahasia untuk user A');
    }

    /** @test */
    public function user_cannot_delete_another_users_session(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();
        $this->actingAs($userA);

        Livewire::test(AgentConsole::class)
            ->call('deleteChat', $chatB->id);

        $this->assertDatabaseHas('agent_chats', ['id' => $chatB->id, 'user_id' => $userB->id]);
        $this->assertSame(2, AgentChatMessage::where('agent_chat_id', $chatB->id)->count());
    }

    /** @test */
    public function user_cannot_confirm_delete_for_another_users_session(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();
        $this->actingAs($userA);

        Livewire::test(AgentConsole::class)
            ->call('confirmDeleteChat', $chatB->id)
            ->assertSet('confirmingDelete', false)
            ->assertSet('deleteTargetId', null);
    }

    /** @test */
    public function tampering_chat_id_to_foreign_session_is_rejected(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();
        $this->actingAs($userA);

        Livewire::test(AgentConsole::class)
            ->set('chatId', $chatB->id)
            ->assertSet('chatId', null)
            ->assertDontSee('balasan rahasia untuk user B');
    }

    /** @test */
    public function send_with_tampered_foreign_chat_id_creates_own_session_not_foreign(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();
        $this->actingAs($userA);

        $beforeB = AgentChatMessage::where('agent_chat_id', $chatB->id)->count();

        $component = Livewire::test(AgentConsole::class)
            // Force foreign chatId (updatedChatId should null it out).
            ->set('chatId', $chatB->id)
            ->assertSet('chatId', null)
            ->set('input', 'halo dari user A setelah tamper')
            ->call('send');

        $newChatId = $component->get('chatId');
        $this->assertNotNull($newChatId);
        $this->assertNotSame($chatB->id, $newChatId);

        $this->assertDatabaseHas('agent_chats', [
            'id' => $newChatId,
            'user_id' => $userA->id,
            'title' => 'halo dari user A setelah tamper',
        ]);

        $this->assertDatabaseHas('agent_chat_messages', [
            'agent_chat_id' => $newChatId,
            'role' => 'user',
            'content' => 'halo dari user A setelah tamper',
        ]);

        // Foreign session untouched.
        $this->assertSame($beforeB, AgentChatMessage::where('agent_chat_id', $chatB->id)->count());
        $this->assertDatabaseMissing('agent_chat_messages', [
            'agent_chat_id' => $chatB->id,
            'content' => 'halo dari user A setelah tamper',
        ]);
    }

    /** @test */
    public function each_admin_persists_messages_only_into_their_own_session(): void
    {
        $userA = $this->makeAdmin('WriterA');
        $userB = $this->makeAdmin('WriterB');

        $this->actingAs($userA);
        $compA = Livewire::test(AgentConsole::class)
            ->set('input', 'pertanyaan A')
            ->call('send');
        $chatIdA = $compA->get('chatId');

        $this->actingAs($userB);
        $compB = Livewire::test(AgentConsole::class)
            ->set('input', 'pertanyaan B')
            ->call('send');
        $chatIdB = $compB->get('chatId');

        $this->assertNotSame($chatIdA, $chatIdB);

        $this->assertDatabaseHas('agent_chats', ['id' => $chatIdA, 'user_id' => $userA->id]);
        $this->assertDatabaseHas('agent_chats', ['id' => $chatIdB, 'user_id' => $userB->id]);

        $this->assertDatabaseHas('agent_chat_messages', [
            'agent_chat_id' => $chatIdA,
            'content' => 'pertanyaan A',
        ]);
        $this->assertDatabaseHas('agent_chat_messages', [
            'agent_chat_id' => $chatIdB,
            'content' => 'pertanyaan B',
        ]);

        // Cross-contamination must not occur.
        $this->assertDatabaseMissing('agent_chat_messages', [
            'agent_chat_id' => $chatIdA,
            'content' => 'pertanyaan B',
        ]);
        $this->assertDatabaseMissing('agent_chat_messages', [
            'agent_chat_id' => $chatIdB,
            'content' => 'pertanyaan A',
        ]);
    }

    /** @test */
    public function switching_users_never_inherits_previous_users_livewire_session(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();

        $this->actingAs($userA);
        Livewire::test(AgentConsole::class)
            ->assertSet('chatId', $chatA->id)
            ->assertSee('pesan rahasia milik user A');

        // Fresh component as user B (new request / new Livewire tree).
        $this->actingAs($userB);
        Livewire::test(AgentConsole::class)
            ->assertSet('chatId', $chatB->id)
            ->assertSee('pesan rahasia milik user B')
            ->assertDontSee('pesan rahasia milik user A')
            ->assertDontSee('Secret chat of A');
    }

    /** @test */
    public function search_does_not_surface_other_users_session_titles(): void
    {
        [$userA, $chatA, $userB, $chatB] = $this->twoUsersWithChats();
        $this->actingAs($userA);

        Livewire::test(AgentConsole::class)
            ->set('search', 'Secret')
            ->assertSee('Secret chat of A')
            ->assertDontSee('Secret chat of B');
    }

    /** @test */
    public function pending_action_state_is_not_shared_across_users(): void
    {
        $userA = $this->makeAdmin('PendingA');
        $userB = $this->makeAdmin('PendingB');

        $pendingForA = [
            'type' => 'update',
            'summary' => 'Update project X for A only',
            'model' => 'project',
            'id' => 1,
            'diff' => [
                [
                    'label' => 'Project #1',
                    'before' => ['status' => 'active'],
                    'after' => ['status' => 'paused'],
                ],
            ],
        ];

        $this->actingAs($userA);
        $compA = Livewire::test(AgentConsole::class)
            ->set('pendingAction', $pendingForA)
            ->assertSee('Update project X for A only');

        // New Livewire instance as user B must start with no pending action.
        $this->actingAs($userB);
        Livewire::test(AgentConsole::class)
            ->assertSet('pendingAction', null)
            ->assertDontSee('Update project X for A only');

        // A's component instance still holds only its own pending action.
        $compA->assertSet('pendingAction.summary', 'Update project X for A only');
    }
}
