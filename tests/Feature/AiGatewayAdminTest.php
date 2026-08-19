<?php

namespace Tests\Feature;

use App\Http\Livewire\Ai\ApplicationDetailPage;
use App\Http\Livewire\Ai\ApplicationsPage;
use App\Http\Livewire\Ai\UsagePage;
use App\Http\Livewire\Table\AiApplications;
use App\Http\Livewire\Table\AiRequests;
use App\Models\AiApplication;
use App\Models\AiModel;
use App\Models\AiRequest;
use App\Models\AiUsage;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class AiGatewayAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Permission::updateOrCreate(['name' => 'VIEW PROJECT'], ['model' => 'PROJECT']);
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'admin', 'description' => 'Admin']);
        foreach (Permission::all() as $perm) {
            PermissionRole::updateOrCreate([
                'role_id' => $role->id,
                'permission_id' => $perm->id,
            ]);
        }
        $user = User::create([
            'name' => 'Admin',
            'email' => uniqid('a').'@test.com',
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
    public function admin_can_open_gateway_pages(): void
    {
        $this->admin();
        Http::fake(['*' => Http::response(['data' => []], 200)]);

        $this->get(route('ai.applications'))->assertOk()->assertSee('Applications')->assertSee('Usage');
        $this->get(route('ai.usage'))
            ->assertOk()
            ->assertSee('AI Usage Graph')
            ->assertSee('Usage')
            ->assertSee('Applications');
        $this->get(route('ai.requests'))->assertRedirect(url('/ai/applications'));
        $this->get(route('ai.logs'))
            ->assertOk()
            ->assertSee('AI Activity Log')
            ->assertSee('Log');
        $this->get(route('ai.settings'))
            ->assertOk()
            ->assertSee('Upstream endpoint')
            ->assertSee('Base URL');
        $this->get(route('ai.docs'))
            ->assertOk()
            ->assertSee('swagger-ui')
            ->assertSee('Applications')
            ->assertSee('API Docs')
            ->assertSee('Endpoint')
            ->assertSee('Docs')
            ->assertSee('Telixcel AI Gateway')
            ->assertSee('aiChatCompletions');
        $this->get(route('ai.docs', ['tab' => 'docs']))
            ->assertOk()
            ->assertSee('How AI Manager works')
            ->assertSee('Request lifecycle')
            ->assertSee('POST /api/v1/ai/chat/completions')
            ->assertDontSee('swagger-ui-bundle');
        $this->get(route('ai.docs.spec'))
            ->assertOk()
            ->assertHeader('content-type', 'application/json; charset=UTF-8')
            ->assertJsonPath('paths./ai/chat/completions.post.operationId', 'aiChatCompletions');

        $app = AiApplication::factory()->create(['name' => 'Hireach']);
        $this->get(route('ai.applications.show', $app))
            ->assertOk()
            ->assertSee('Settings')
            ->assertSee('Usage')
            ->assertSee('Requests')
            ->assertSee('Test')
            ->assertSee('Allowed models')
            ->assertSee('Client connection')
            ->assertSee('Endpoint')
            ->assertSee(rtrim((string) config('app.url'), '/'))
            ->assertSee(url('/api/v1/ai/chat/completions'))
            ->assertSee('Show key')
            ->assertDontSee($app->revealedKey());
        $this->get(route('ai.applications.usage', $app))
            ->assertOk()
            ->assertSee('Overview')
            ->assertSee('Details')
            ->assertSee('AI Usage Graph')
            ->assertSee('Requests');
        $this->get(route('ai.applications.requests', $app))->assertOk()->assertSee('Request ID');
        $this->get(route('ai.applications.test', $app))
            ->assertOk()
            ->assertSee('Send test')
            ->assertDontSee('Allowed models');
    }

    /** @test */
    public function api_docs_guide_supports_english_and_indonesian(): void
    {
        $this->admin();

        $this->get(route('ai.docs', ['tab' => 'docs']))
            ->assertOk()
            ->assertSee('How AI Manager works')
            ->assertSee('Request lifecycle');

        $this->withSession(['locale' => 'id'])
            ->get(route('ai.docs', ['tab' => 'docs']))
            ->assertOk()
            ->assertSee('Cara kerja AI Manager')
            ->assertSee('Siklus permintaan')
            ->assertSee('Dokumen API');
    }

    /** @test */
    public function admin_can_create_application_and_see_key_once(): void
    {
        $this->admin();

        Livewire::test(ApplicationsPage::class)
            ->set('input.name', 'Hireach')
            ->call('save')
            ->assertRedirect();

        $app = AiApplication::where('slug', 'hireach')->first();
        $this->assertNotNull($app);
        $this->assertSame('Hireach', $app->name);
        $this->assertSame('active', $app->status);
        $this->assertTrue((bool) $app->require_end_user);
        $this->assertNotNull($app->api_key_hash);
        $this->assertTrue(session()->has('ai_plain_api_key'));
        $this->assertStringStartsWith('sk-', session('ai_plain_api_key'));

        Http::fake(['*' => Http::response(['data' => []], 200)]);
        $this->assertNotNull($app->uuid);
        $this->assertSame(url('/ai/applications/'.$app->uuid), route('ai.applications.show', $app));
        $this->get(route('ai.applications.show', $app))
            ->assertOk()
            ->assertSee('Hireach')
            ->assertSee('Allowed models')
            ->assertSee('Settings');
        $this->get('/ai/applications/'.$app->id)->assertNotFound();
    }

    /** @test */
    public function application_detail_shows_usage_for_that_app(): void
    {
        $this->admin();
        Http::fake(['*' => Http::response(['data' => []], 200)]);

        $app = AiApplication::factory()->create(['name' => 'Hireach', 'slug' => 'hireach-usage']);
        AiUsage::create([
            'ai_application_id' => $app->id,
            'model' => 'kr/auto',
            'input_tokens' => 10,
            'output_tokens' => 20,
            'total_tokens' => 30,
            'cost' => 0.0012,
            'currency' => 'USD',
            'end_user_id' => '42',
            'end_user_name' => 'Budi',
        ]);

        $this->get(route('ai.applications.usage', $app->uuid))
            ->assertOk()
            ->assertSee('Overview')
            ->assertSee('Details')
            ->assertSee('kr/auto')
            ->assertSee('Usage by model')
            ->assertSee('Usage by customer')
            ->assertSee('Tokens');

        Livewire::test(UsagePage::class, ['application' => $app])
            ->assertSee('Requests')
            ->assertSee('kr/auto')
            ->assertSee('Telixcel Gateway')
            ->assertSee('AI routing')
            ->call('selectNode', 'model', 'kr/auto')
            ->assertSet('inspectModel', 'kr/auto')
            ->assertSee('Average latency')
            ->assertSee('ai-ug-modal-scrim', false)
            ->call('clearInspect')
            ->assertSet('inspectModel', '')
            ->assertDontSee('Average latency')
            ->call('setTab', 'details')
            ->assertSee('Budi')
            ->assertSee('42')
            ->assertSee('Customers inside this app');
    }

    /** @test */
    public function general_usage_shows_all_applications(): void
    {
        $this->admin();
        Http::fake(['*' => Http::response(['data' => []], 200)]);

        $hireach = AiApplication::factory()->create(['name' => 'Hireach', 'slug' => 'hireach-global']);
        $other = AiApplication::factory()->create(['name' => 'BillingBot', 'slug' => 'billing-bot']);
        AiUsage::create([
            'ai_application_id' => $hireach->id,
            'model' => 'kr/auto',
            'input_tokens' => 10,
            'output_tokens' => 20,
            'total_tokens' => 30,
            'cost' => 0.0012,
            'currency' => 'USD',
        ]);
        AiUsage::create([
            'ai_application_id' => $other->id,
            'model' => 'kr/claude-haiku-4.5',
            'input_tokens' => 5,
            'output_tokens' => 5,
            'total_tokens' => 10,
            'cost' => 0.0004,
            'currency' => 'USD',
        ]);

        $this->get(route('ai.usage'))
            ->assertOk()
            ->assertSee('Hireach')
            ->assertSee('BillingBot')
            ->assertSee('kr/auto')
            ->assertSee('kr/claude-haiku-4.5')
            ->assertSee('Telixcel Gateway');

        Livewire::test(UsagePage::class)
            ->assertSee('Hireach')
            ->assertSee('BillingBot')
            ->call('setTab', 'details')
            ->assertSee('By application')
            ->assertSee('Customers');
    }

    /** @test */
    public function applications_table_shows_this_month_usage(): void
    {
        $this->admin();
        $app = AiApplication::factory()->create(['name' => 'Hireach', 'slug' => 'hireach-list', 'cost_currency' => 'USD']);
        AiUsage::create([
            'ai_application_id' => $app->id,
            'model' => 'kr/auto',
            'input_tokens' => 80,
            'output_tokens' => 70,
            'total_tokens' => 150,
            'cost' => 0.02,
            'currency' => 'USD',
        ]);

        Livewire::test(AiApplications::class)
            ->assertSee('Usage')
            ->assertSee('Hireach')
            ->assertSee('150 tok')
            ->assertSee('$0.02');
    }

    /** @test */
    public function application_key_is_hidden_until_password_is_confirmed(): void
    {
        $this->admin();
        Http::fake(['*' => Http::response(['data' => []], 200)]);

        $app = AiApplication::factory()->create(['name' => 'Hireach', 'slug' => 'hireach-key']);
        $raw = $app->issueKey();
        $app->save();

        Livewire::test(ApplicationDetailPage::class, ['application' => $app->fresh()])
            ->assertSee('Show key')
            ->assertDontSee($raw)
            ->assertSet('plainApiKey', session('ai_plain_api_key'));

        $this->withSession(['auth.password_confirmed_at' => time()]);

        Livewire::test(ApplicationDetailPage::class, ['application' => $app->fresh()])
            ->call('revealKey')
            ->assertSet('plainApiKey', $raw)
            ->assertSee($raw);
    }

    /** @test */
    public function wrong_password_does_not_reveal_application_key(): void
    {
        $this->admin();
        Http::fake(['*' => Http::response(['data' => []], 200)]);

        $app = AiApplication::factory()->create(['name' => 'Hireach', 'slug' => 'hireach-pw']);
        $raw = $app->issueKey();
        $app->save();

        Livewire::test(ApplicationDetailPage::class, ['application' => $app->fresh()])
            ->set('confirmablePassword', 'not-the-password')
            ->call('confirmPassword')
            ->assertHasErrors('confirmable_password')
            ->assertDontSee($raw);
    }

    /** @test */
    public function admin_can_restrict_application_to_upstream_models(): void
    {
        $this->admin();
        config([
            'ai.base_url' => 'https://router.test/v1',
            'ai.api_key' => 'test-key',
        ]);
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/models')) {
                return Http::response([
                    'data' => [
                        ['id' => 'kr/claude-sonnet-4.5', 'name' => 'Claude Sonnet 4.5', 'owned_by' => 'Kiro'],
                        ['id' => 'openai/gpt-4o', 'name' => 'GPT-4o', 'owned_by' => 'OpenAI'],
                    ],
                ], 200);
            }

            return Http::response([
                'choices' => [['message' => ['role' => 'assistant', 'content' => 'pong']]],
            ], 200);
        });

        $app = AiApplication::factory()->create(['name' => 'Hireach', 'slug' => 'hireach-models']);

        $component = Livewire::test(ApplicationDetailPage::class, ['application' => $app])
            ->call('recheckModels')
            ->assertSee('Allowed models')
            ->assertSee('Claude Sonnet 4.5')
            ->assertSee('Kiro')
            ->assertSee('GPT-4o')
            ->assertSee('OpenAI');

        $allowed = AiModel::query()->where('model_identifier', 'kr/claude-sonnet-4.5')->first();
        $this->assertNotNull($allowed);
        $this->assertSame('Claude Sonnet 4.5', $allowed->display_name);
        $this->assertSame('kiro', $allowed->provider);
        $this->assertSame('Kiro', $allowed->sourceLabel());

        $component->set('selectedModels', [(string) $allowed->id])
            ->call('save');

        $this->assertTrue($app->fresh()->models()->where('ai_models.id', $allowed->id)->exists());
        $this->assertSame(1, $app->fresh()->models()->count());
    }

    /** @test */
    public function unusable_upstream_models_are_hidden_from_applications(): void
    {
        $this->admin();
        config([
            'ai.base_url' => 'https://router.test/v1',
            'ai.api_key' => 'test-key',
        ]);
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/models')) {
                return Http::response([
                    'data' => [
                        ['id' => 'kr/claude-haiku-4.5', 'name' => 'Claude Haiku 4.5', 'owned_by' => 'Kiro'],
                        ['id' => 'ag/broken-model', 'name' => 'Broken Model', 'owned_by' => 'Ag'],
                    ],
                ], 200);
            }

            $model = $request->data()['model'] ?? '';
            if ($model === 'ag/broken-model') {
                return Http::response(['error' => ['message' => 'model unavailable']], 404);
            }

            return Http::response([
                'choices' => [['message' => ['role' => 'assistant', 'content' => 'pong']]],
            ], 200);
        });

        $app = AiApplication::factory()->create(['name' => 'Hireach', 'slug' => 'hireach-probe']);

        Livewire::test(ApplicationDetailPage::class, ['application' => $app])
            ->call('recheckModels')
            ->assertSee('Claude Haiku 4.5')
            ->assertDontSee('Broken Model')
            ->assertSee('1 model yang dapat digunakan');

        $this->assertTrue(AiModel::query()->where('model_identifier', 'kr/claude-haiku-4.5')->where('enabled', true)->exists());
        $this->assertTrue(AiModel::query()->where('model_identifier', 'ag/broken-model')->where('enabled', false)->exists());
    }

    /** @test */
    public function telixcel_labels_kr_as_kiro_and_hides_prefix_from_public_name(): void
    {
        $model = new AiModel([
            'model_identifier' => 'kr/claude-sonnet-4.5',
            'display_name' => 'kr/claude-sonnet-4.5',
            'provider' => 'kr',
        ]);

        $this->assertSame('Claude Sonnet 4.5', $model->publicName());
        $this->assertSame('Kiro', $model->sourceLabel());
        $this->assertSame('kr', $model->sourceKey());
    }

    /** @test */
    public function admin_can_find_dika_in_request_log(): void
    {
        $this->admin();
        $app = AiApplication::factory()->create(['name' => 'Hireach', 'slug' => 'hireach-log']);
        AiModel::factory()->create();

        AiRequest::create([
            'request_id' => 'req_ai_dika_test',
            'ai_application_id' => $app->id,
            'model' => 'anthropic/claude-sonnet-4',
            'stream' => false,
            'status' => AiRequest::STATUS_SUCCESS,
            'end_user_id' => '42',
            'end_user_name' => 'Dika',
            'end_user_email' => 'dika@hireach.test',
            'feature' => 'cv-assistant',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        Livewire::test(AiRequests::class, ['applicationId' => $app->id])
            ->set('search', 'Dika')
            ->assertSee('Dika');
    }

    /** @test */
    public function ai_records_get_a_uuid_when_created(): void
    {
        $app = AiApplication::factory()->create();
        $this->assertNotEmpty($app->uuid);

        $model = AiModel::factory()->create(['model_identifier' => 'test/uuid-model']);
        $this->assertNotEmpty($model->uuid);

        $request = AiRequest::create([
            'request_id' => 'req_ai_uuid_test',
            'ai_application_id' => $app->id,
            'model' => 'test/uuid-model',
            'status' => AiRequest::STATUS_SUCCESS,
        ]);
        $this->assertNotEmpty($request->uuid);

        $usage = AiUsage::create([
            'ai_application_id' => $app->id,
            'ai_request_id' => $request->id,
            'model' => 'test/uuid-model',
            'total_tokens' => 3,
        ]);
        $this->assertNotEmpty($usage->uuid);
    }
}
