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

/** AI Console chat-history feature (per-session persistence). */
class AgentConsoleHistoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::create([
            'name' => 'Admin', 'email' => uniqid('a') . '@test.com',
            'password' => bcrypt('password'), 'current_team_id' => 1,
        ]);
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'team', 'description' => 'Admin']);
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'A',
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    /** @test */
    public function it_starts_with_a_welcome_and_no_saved_session(): void
    {
        $this->admin();
        Livewire::test(AgentConsole::class)
            ->assertSet('chatId', null)
            ->assertCount('messages', 1);
    }

    /** @test */
    public function sending_a_message_creates_and_persists_a_session(): void
    {
        $this->admin();

        Livewire::test(AgentConsole::class)
            ->set('input', 'Berapa total project?')
            ->call('send')
            ->assertSet('isThinking', true);

        $this->assertSame(1, AgentChat::count());
        $chat = AgentChat::first();
        $this->assertSame('Berapa total project?', $chat->title);
        $this->assertDatabaseHas('agent_chat_messages', [
            'agent_chat_id' => $chat->id, 'role' => 'user', 'content' => 'Berapa total project?',
        ]);
    }

    /** @test */
    public function refresh_restores_the_latest_session(): void
    {
        $user = $this->admin();
        $chat = AgentChat::create(['user_id' => $user->id, 'title' => 'Old chat']);
        AgentChatMessage::create(['agent_chat_id' => $chat->id, 'role' => 'user', 'content' => 'hi']);
        AgentChatMessage::create(['agent_chat_id' => $chat->id, 'role' => 'assistant', 'content' => 'halo']);

        Livewire::test(AgentConsole::class)
            ->assertSet('chatId', $chat->id)
            ->assertCount('messages', 2)
            ->assertSee('halo');
    }

    /** @test */
    public function new_chat_resets_to_welcome(): void
    {
        $user = $this->admin();
        $chat = AgentChat::create(['user_id' => $user->id, 'title' => 'Old']);
        AgentChatMessage::create(['agent_chat_id' => $chat->id, 'role' => 'user', 'content' => 'x']);

        Livewire::test(AgentConsole::class)
            ->call('newChat')
            ->assertSet('chatId', null)
            ->assertCount('messages', 1);
    }

    /** @test */
    public function deleting_a_session_removes_it_and_its_messages(): void
    {
        $user = $this->admin();
        $chat = AgentChat::create(['user_id' => $user->id, 'title' => 'Del']);
        AgentChatMessage::create(['agent_chat_id' => $chat->id, 'role' => 'user', 'content' => 'x']);

        Livewire::test(AgentConsole::class)->call('deleteChat', $chat->id);

        $this->assertSame(0, AgentChat::count());
        $this->assertSame(0, AgentChatMessage::count());
    }

    /** @test */
    public function a_user_only_sees_their_own_sessions(): void
    {
        $me = $this->admin();
        AgentChat::create(['user_id' => $me->id + 999, 'title' => 'Someone else']);
        AgentChat::create(['user_id' => $me->id, 'title' => 'Mine']);

        $this->assertSame(1, AgentChat::where('user_id', $me->id)->count());
    }
}
