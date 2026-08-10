<?php

namespace Tests\Feature;

use App\Models\AgentUserToken;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentUserTokenTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $user = User::create([
            'name' => 'U', 'email' => uniqid('u') . '@t.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'team', 'description' => 'A']);
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'A',
        ]);

        return $user->fresh();
    }

    /** @test */
    public function first_chat_mints_token_with_read_and_update_limited(): void
    {
        $user = $this->user();
        [$row, $plain] = AgentUserToken::ensureForUser($user);

        $this->assertStringStartsWith('agt_', $plain);
        $this->assertSame($user->id, $row->user_id);
        $this->assertContains('read', $row->abilities);
        $this->assertContains('update_limited', $row->abilities);
        $this->assertTrue($row->allowsTool('query_records'));
        $this->assertTrue($row->allowsTool('update_record'));
        $this->assertFalse($row->allowsTool('delete_everything'));

        // second call reuses same token
        [$row2, $plain2] = AgentUserToken::ensureForUser($user);
        $this->assertSame($row->id, $row2->id);
        $this->assertSame($plain, $plain2);
    }

    /** @test */
    public function api_accepts_user_token_and_rejects_unknown(): void
    {
        $user = $this->user();
        [, $plain] = AgentUserToken::ensureForUser($user);

        $this->withHeaders(['X-Agent-Token' => $plain])
            ->postJson('/api/agent', ['action' => 'health'])
            ->assertOk()
            ->assertJsonPath('user_id', $user->id);

        $this->withHeaders(['X-Agent-Token' => 'agt_invalidtokenxxxxxxxxxxxxxxxx'])
            ->postJson('/api/agent', ['action' => 'health'])
            ->assertStatus(401);
    }
}
