<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Task;
use App\Models\User;
use App\Services\Agent\AgentRunner;
use App\Services\Agent\PendingActionStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** AI Console → AgentRunner (hermes driver, simple chat/completions). */
class HermesAgentServiceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        foreach (['PROJECT', 'TASK'] as $m) {
            foreach (['VIEW', 'CREATE', 'UPDATE', 'DELETE', 'EDIT'] as $a) {
                Permission::updateOrCreate(['name' => "{$a} {$m}"], ['model' => $m]);
            }
        }
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'admin', 'description' => 'Admin']);
        foreach (Permission::all() as $perm) {
            \App\Models\PermissionRole::updateOrCreate([
                'role_id' => $role->id,
                'permission_id' => $perm->id,
            ]);
        }
        $user = User::create([
            'name' => 'Admin',
            'email' => uniqid('a') . '@test.com',
            'password' => bcrypt('password'),
            'current_team_id' => 1,
        ]);
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'A',
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    /** @test */
    public function hermes_driver_posts_chat_completions_non_stream(): void
    {
        config([
            'services.ai.driver' => 'hermes',
            'services.ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'services.ai.api_key' => 'test-key',
            'services.ai.model' => 'telixcel',
            'services.ai.timeout' => 30,
        ]);

        $user = $this->admin();

        Http::fake([
            'ai.test/*' => Http::response([
                'id' => 'chatcmpl_1',
                'model' => 'telixcel',
                'choices' => [
                    ['index' => 0, 'message' => ['role' => 'assistant', 'content' => 'Halo simple API'], 'finish_reason' => 'stop'],
                ],
            ], 200),
        ]);

        $result = app(AgentRunner::class)->run([], 'Berapa project aktif?', 42);

        $this->assertSame('Halo simple API', $result['reply']);
        $this->assertSame('hermes', $result['driver']);
        $this->assertNull($result['pending']);
        $this->assertSame('http://ai.test/v1/chat/completions', AgentRunner::endpoint());

        Http::assertSent(function ($request) use ($user) {
            $body = $request->data();
            $headers = $request->headers();
            $sessionKey = $headers['X-Hermes-Session-Key'][0] ?? ($headers['x-hermes-session-key'][0] ?? null);
            $msgs = $body['messages'] ?? [];
            $last = end($msgs);

            return $request->url() === 'http://ai.test/v1/chat/completions'
                && ($body['stream'] ?? true) === false
                && str_contains((string) ($last['content'] ?? ''), 'Berapa project aktif?')
                && str_contains((string) $sessionKey, "user-{$user->id}:chat-42");
        });
    }

    /** @test */
    public function task_list_request_goes_through_hermes_with_snapshot_context(): void
    {
        config([
            'services.ai.driver' => 'hermes',
            'services.ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'services.ai.api_key' => 'test-key',
            'services.ai.model' => 'telixcel',
            'services.ai.timeout' => 30,
        ]);
        $user = $this->admin();

        Task::create([
            'project_id' => null,
            'title' => 'Write Articles',
            'type' => 'operasional',
            'status' => 'pending',
            'priority' => 'medium',
            'team_id' => 1,
            'owner_id' => $user->id,
            'assigned_to' => $user->id,
            'target_date' => now()->addDays(7),
        ]);

        Http::fake([
            'ai.test/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'role' => 'assistant',
                        'content' => "| ID | Judul |\n|---:|:------|\n| 1 | Write Articles |",
                    ],
                    'finish_reason' => 'stop',
                ]],
            ], 200),
        ]);

        $result = app(AgentRunner::class)->run([], 'berikan table task yang ada', null);

        $this->assertSame('hermes', $result['driver']);
        $this->assertStringContainsString('Write Articles', $result['reply']);
        Http::assertSent(function ($request) {
            $body = $request->data();
            $msgs = $body['messages'] ?? [];
            $blob = json_encode($msgs);

            // Snapshot is in context for the model; reply is not short-circuited locally.
            return str_contains((string) $blob, 'data_snapshot')
                && str_contains((string) $blob, 'Write Articles')
                && str_contains((string) $blob, 'Markdown table');
        });
    }

    /** @test */
    public function hermes_path_picks_up_pending_from_api_store(): void
    {
        config([
            'services.ai.driver' => 'hermes',
            'services.ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'services.ai.api_key' => 'test-key',
            'services.ai.model' => 'telixcel',
            'services.ai.timeout' => 30,
        ]);

        $user = $this->admin();

        // Simulate Hermes calling POST /api/agent *during* the chat request,
        // which writes the pending proposal for this user mid-run.
        Http::fake(function ($request) use ($user) {
            PendingActionStore::put($user->id, [
                'type' => 'update',
                'model' => 'task',
                'ids' => [99],
                'values' => ['status' => 'complete'],
                'diff' => [],
                'summary' => 'Update 1 Task record(s).',
            ]);

            return Http::response([
                'choices' => [
                    ['message' => ['role' => 'assistant', 'content' => 'Sudah diajukan approval.'], 'finish_reason' => 'stop'],
                ],
            ], 200);
        });

        $result = app(AgentRunner::class)->run([], 'cek status project saya', null);

        $this->assertSame('hermes', $result['driver']);
        $this->assertNotNull($result['pending']);
        $this->assertSame([99], $result['pending']['ids']);
        // Stays until approve/reject.
        $this->assertNotNull(PendingActionStore::get($user->id));
    }

    /** @test */
    public function status_change_returns_pending_immediately(): void
    {
        config(['services.ai.driver' => 'hermes']);
        $user = $this->admin();

        $task = Task::create([
            'project_id' => null,
            'title' => 'Write Articles',
            'type' => 'operasional',
            'status' => 'pending',
            'priority' => 'medium',
            'team_id' => 1,
            'owner_id' => $user->id,
            'target_date' => now()->addDays(7),
        ]);

        Http::fake();

        $result = app(AgentRunner::class)->run([], 'Write Articles ubah jadi complete', null);

        $this->assertSame('local', $result['driver']);
        $this->assertNotNull($result['pending']);
        $this->assertContains($task->id, $result['pending']['ids']);
        $this->assertSame('complete', $result['pending']['values']['status']);
        $this->assertNotNull(PendingActionStore::get($user->id));
        Http::assertNothingSent();
    }

    /** @test */
    public function short_follow_up_resolves_task_from_history(): void
    {
        config(['services.ai.driver' => 'hermes']);
        $user = $this->admin();

        $task = Task::create([
            'project_id' => null,
            'title' => 'Write Articles',
            'type' => 'operasional',
            'status' => 'pending',
            'priority' => 'medium',
            'team_id' => 1,
            'owner_id' => $user->id,
            'target_date' => now()->addDays(7),
        ]);

        Http::fake();

        $history = [
            ['role' => 'user', 'content' => 'status Write Articles'],
            ['role' => 'assistant', 'content' => "Status Write Articles masih pending. task ID {$task->id}."],
        ];

        $result = app(AgentRunner::class)->run($history, 'coba ubah jadi complete ya', null);

        $this->assertSame('local', $result['driver']);
        $this->assertNotNull($result['pending']);
        $this->assertContains($task->id, $result['pending']['ids']);
    }
}
