<?php

namespace Tests\Feature;

use App\Models\AgentActionLog;
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

/** AI Console → AgentRunner (AI + model-chosen tools). */
class AiAgentServiceTest extends TestCase
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
    public function ai_posts_chat_completions_with_tools(): void
    {
        config([
            'ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'ai.api_key' => 'test-key',
            'ai.model' => 'telixcel',
            'ai.timeout' => 30,
            'ai.max_iterations' => 6,
        ]);

        $user = $this->admin();

        Http::fake([
            'ai.test/*' => Http::response([
                'id' => 'chatcmpl_1',
                'model' => 'telixcel',
                'choices' => [
                    ['index' => 0, 'message' => ['role' => 'assistant', 'content' => 'Halo'], 'finish_reason' => 'stop'],
                ],
            ], 200),
        ]);

        $result = app(AgentRunner::class)->run([], 'Halo apa kabar?', 42);

        $this->assertSame('Halo', $result['reply']);
        $this->assertSame('ai', $result['driver']);
        $this->assertNull($result['pending']);
        $this->assertSame('http://ai.test/v1/chat/completions', AgentRunner::endpoint());

        Http::assertSent(function ($request) use ($user) {
            $body = $request->data();
            $headers = $request->headers();
            $sessionKey = $headers['X-AI-Session-Key'][0] ?? ($headers['x-ai-session-key'][0] ?? null);
            $tools = $body['tools'] ?? [];
            $names = collect($tools)->map(fn ($t) => $t['function']['name'] ?? null)->filter()->all();

            return $request->url() === 'http://ai.test/v1/chat/completions'
                && ($body['stream'] ?? true) === false
                && ($body['tool_choice'] ?? null) === 'auto'
                && in_array('query_records', $names, true)
                && in_array('update_record', $names, true)
                && str_contains((string) $sessionKey, "user-{$user->id}:chat-42");
        });
    }

    /** @test */
    public function model_tool_call_query_records_then_final_reply(): void
    {
        config([
            'ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'ai.api_key' => 'test-key',
            'ai.model' => 'telixcel',
            'ai.timeout' => 30,
            'ai.max_iterations' => 6,
        ]);
        $user = $this->admin();

        $task = Task::create([
            'project_id' => null,
            'title' => 'Write Articles',
            'type' => 'operasional',
            'status' => 'complete',
            'priority' => 'medium',
            'team_id' => 1,
            'owner_id' => $user->id,
            'assigned_to' => $user->id,
            'target_date' => now()->addDays(7),
        ]);

        $callCount = 0;
        Http::fake(function ($request) use (&$callCount, $task) {
            $callCount++;
            if ($callCount === 1) {
                return Http::response([
                    'choices' => [[
                        'message' => [
                            'role' => 'assistant',
                            'content' => null,
                            'tool_calls' => [[
                                'id' => 'call_1',
                                'type' => 'function',
                                'function' => [
                                    'name' => 'query_records',
                                    'arguments' => json_encode([
                                        'model' => 'task',
                                        'filters' => [
                                            ['field' => 'title', 'op' => 'like', 'value' => 'Write Articles'],
                                        ],
                                        'limit' => 10,
                                    ]),
                                ],
                            ]],
                        ],
                        'finish_reason' => 'tool_calls',
                    ]],
                ], 200);
            }

            // Second call: model sees tool result and answers
            $body = $request->data();
            $msgs = $body['messages'] ?? [];
            $toolMsg = collect($msgs)->first(fn ($m) => ($m['role'] ?? '') === 'tool');
            $this->assertNotNull($toolMsg);
            $this->assertStringContainsString('Write Articles', (string) ($toolMsg['content'] ?? ''));

            return Http::response([
                'choices' => [[
                    'message' => [
                        'role' => 'assistant',
                        'content' => "Task #{$task->id} Write Articles status: complete",
                    ],
                    'finish_reason' => 'stop',
                ]],
            ], 200);
        });

        $result = app(AgentRunner::class)->run([], 'status Write Articles', null);

        $this->assertSame('ai', $result['driver']);
        $this->assertStringContainsString('complete', $result['reply']);
        $this->assertContains('query_records', $result['metrics']['tools_called'] ?? []);
        $this->assertSame(2, $callCount);
    }

    /** @test */
    public function model_update_record_returns_pending_approval(): void
    {
        config([
            'ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'ai.api_key' => 'test-key',
            'ai.model' => 'telixcel',
            'ai.timeout' => 30,
            'ai.max_iterations' => 6,
        ]);
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

        Http::fake([
            'ai.test/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'role' => 'assistant',
                        'content' => null,
                        'tool_calls' => [[
                            'id' => 'call_u1',
                            'type' => 'function',
                            'function' => [
                                'name' => 'update_record',
                                'arguments' => json_encode([
                                    'model' => 'task',
                                    'id' => $task->id,
                                    'values' => ['status' => 'complete'],
                                ]),
                            ],
                        ]],
                    ],
                    'finish_reason' => 'tool_calls',
                ]],
            ], 200),
        ]);

        $result = app(AgentRunner::class)->run([], 'Write Articles ubah jadi complete', null);

        $this->assertSame('ai', $result['driver']);
        $this->assertNotNull($result['pending']);
        $this->assertContains($task->id, $result['pending']['ids']);
        $this->assertSame('complete', $result['pending']['values']['status']);
        $this->assertStringContainsString('konfirmasi', strtolower($result['reply']));
        $this->assertNotNull(PendingActionStore::get($user->id));
        $this->assertContains('update_record', $result['metrics']['tools_called'] ?? []);
    }

    /** @test */
    public function jadiin_complete_local_path_shows_pending_card(): void
    {
        $user = $this->admin();

        $task = Task::create([
            'project_id' => null,
            'title' => 'Laporan bulanan untuk User B',
            'type' => 'admin',
            'status' => 'pending',
            'priority' => 'medium',
            'team_id' => 1,
            'owner_id' => $user->id,
            'target_date' => now()->addDays(7),
        ]);

        Http::fake(); // must not call AI

        $result = app(AgentRunner::class)->run(
            [],
            'Laporan bulanan untuk User B jadiin complete',
            null
        );

        $this->assertSame('local', $result['driver']);
        $this->assertNotNull($result['pending']);
        $this->assertContains($task->id, $result['pending']['ids']);
        $this->assertSame('complete', $result['pending']['values']['status']);
        $this->assertStringContainsString('kartu', strtolower($result['reply']));
        $this->assertStringNotContainsString('curl', strtolower($result['reply']));
        Http::assertNothingSent();
    }

    /** @test */
    public function list_request_does_not_show_approval_card(): void
    {
        config([
            'ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'ai.api_key' => 'test-key',
            'ai.model' => 'telixcel',
            'ai.timeout' => 30,
            'ai.max_iterations' => 6,
        ]);
        $user = $this->admin();

        Task::create([
            'project_id' => null,
            'title' => 'Public Relations',
            'type' => 'finance',
            'status' => 'pending',
            'priority' => 'medium',
            'team_id' => 1,
            'owner_id' => $user->id,
            'target_date' => now()->addDays(7),
        ]);

        Http::fake([
            'ai.test/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'role' => 'assistant',
                        'content' => "Here are the pending tasks:\n| ID | Judul | Status |\n| 7 | Public Relations | pending |",
                    ],
                    'finish_reason' => 'stop',
                ]],
            ], 200),
        ]);

        $result = app(AgentRunner::class)->run([], 'list task', null);

        $this->assertNull($result['pending'], 'list must not open approval card');
        $this->assertStringNotContainsString('Terapkan', $result['reply']);
    }

    /** @test */
    public function ai_text_only_update_still_materializes_approval_card(): void
    {
        config([
            'ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'ai.api_key' => 'test-key',
            'ai.model' => 'telixcel',
            'ai.timeout' => 30,
            'ai.max_iterations' => 6,
        ]);
        $user = $this->admin();

        $task = Task::create([
            'project_id' => null,
            'title' => 'Laporan bulanan untuk User B',
            'type' => 'operasional',
            'status' => 'pending',
            'priority' => 'medium',
            'team_id' => 1,
            'owner_id' => $user->id,
            'target_date' => now()->addDays(7),
        ]);

        // Model only *talks* about approve — no tool_calls.
        Http::fake([
            'ai.test/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Update task "Laporan bulanan untuk User B" (ID: '.$task->id.') jadi complete — tunggu lu approve di UI ya.',
                    ],
                    'finish_reason' => 'stop',
                ]],
            ], 200),
        ]);

        $result = app(AgentRunner::class)->run(
            [],
            'ubah Laporan bulanan untuk User B jadi complete',
            null
        );

        $this->assertNotNull($result['pending'], 'pending must be set so Livewire shows the card');
        $this->assertContains($task->id, $result['pending']['ids']);
        $this->assertSame('complete', $result['pending']['values']['status']);
        $this->assertNotNull(PendingActionStore::get($user->id));
    }

    /** @test */
    public function ai_path_picks_up_existing_pending_from_store(): void
    {
        config([
            'ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'ai.api_key' => 'test-key',
            'ai.model' => 'telixcel',
            'ai.timeout' => 30,
        ]);

        $user = $this->admin();

        PendingActionStore::put($user->id, [
            'type' => 'update',
            'model' => 'task',
            'ids' => [99],
            'values' => ['status' => 'complete'],
            'diff' => [],
            'summary' => 'Update 1 Task record(s).',
        ]);

        Http::fake([
            'ai.test/*' => Http::response([
                'choices' => [
                    ['message' => ['role' => 'assistant', 'content' => 'OK'], 'finish_reason' => 'stop'],
                ],
            ], 200),
        ]);

        // List/read must NOT surface a stale approval card.
        $list = app(AgentRunner::class)->run([], 'list task', null);
        $this->assertNull($list['pending']);

        // Update intent may still pick up store if no new proposal.
        $upd = app(AgentRunner::class)->run([], 'ubah #99 jadi complete', null);
        $this->assertSame('ai', $upd['driver']);
        $this->assertNotNull($upd['pending']);
    }

    /** @test */
    public function plain_llm_turn_is_logged_as_chat_action(): void
    {
        $user = $this->admin();
        config([
            'ai.endpoint' => 'http://ai.test/v1/chat/completions',
            'ai.api_key' => 'test-key',
            'ai.model' => 'telixcel',
            'ai.timeout' => 30,
        ]);
        Http::fake([
            'ai.test/*' => Http::response([
                'choices' => [[
                    'message' => ['role' => 'assistant', 'content' => 'Halo'],
                    'finish_reason' => 'stop',
                ]],
            ], 200),
        ]);

        app(AgentRunner::class)->run([], 'Halo apa kabar?', null);

        $log = AgentActionLog::where('tool', 'chat')->first();
        $this->assertNotNull($log);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame('ok', $log->status);
        $this->assertSame('Halo', $log->result['reply'] ?? null);
    }
}
