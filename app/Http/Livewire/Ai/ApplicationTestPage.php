<?php

namespace App\Http\Livewire\Ai;

use App\Exceptions\AiGatewayException;
use App\Http\Livewire\Ai\Concerns\AuthorizesAiAdmin;
use App\Models\AiApplication;
use App\Models\AiModel;
use App\Services\Ai\AiModelService;
use App\Services\Ai\AiPermissionService;
use App\Services\Ai\AiUpstreamService;
use Livewire\Component;

class ApplicationTestPage extends Component
{
    use AuthorizesAiAdmin;

    public AiApplication $application;
    public $testModel = '';
    public $testPrompt = 'Reply with the single word pong.';
    public $testContent = null;
    public $testMeta = null;
    public $testError = null;
    public $modelsError = null;

    public function mount(AiApplication $application): void
    {
        $this->authorizeAiAdmin();
        $this->application = $application->load('models');
        $this->pickDefaultModel();
    }

    public function refreshModels(AiUpstreamService $upstream, AiModelService $models): void
    {
        $this->authorizeAiAdmin();
        $this->modelsError = null;

        if ((string) config('ai.base_url') === '' || (string) config('ai.api_key') === '') {
            $this->modelsError = 'Set AI_BASE_URL and AI_API_KEY first.';
            $this->pickDefaultModel();

            return;
        }

        try {
            $rows = $upstream->listModels();
            $models->syncFromUpstream($rows);
            $ids = [];
            foreach ($rows as $row) {
                $id = trim((string) ($row['id'] ?? ''));
                if ($id !== '') {
                    $ids[] = $id;
                }
            }
            $models->verifyUsable($upstream, $ids, false);
        } catch (AiGatewayException $e) {
            $this->modelsError = $e->getMessage();
        }

        $this->application->load('models');
        $this->pickDefaultModel();
    }

    public function ping(AiUpstreamService $upstream, AiModelService $models, AiPermissionService $permissions): void
    {
        $this->authorizeAiAdmin();
        $this->testContent = null;
        $this->testMeta = null;
        $this->testError = null;

        $this->validate([
            'testModel' => 'required|string|max:191',
            'testPrompt' => 'required|string|max:4000',
        ]);

        try {
            $model = $models->resolveEnabled(trim($this->testModel));
            $permissions->assertAllowed($this->application, $model);
            $body = $upstream->chat([
                'model' => $model->model_identifier,
                'messages' => [
                    ['role' => 'user', 'content' => $this->testPrompt],
                ],
                'max_tokens' => 128,
            ]);
        } catch (AiGatewayException $e) {
            $this->testError = $e->getMessage();

            return;
        }

        $this->testContent = data_get($body, 'choices.0.message.content');
        if (! is_string($this->testContent) || $this->testContent === '') {
            $this->testContent = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        $this->testMeta = [
            'model' => $body['model'] ?? $this->testModel,
            'usage' => $body['usage'] ?? null,
        ];
    }

    public function render()
    {
        $catalog = AiModel::query()->usable()->orderBy('model_identifier')->get();
        $allowedIds = $this->application->models->pluck('id')->map(fn ($id) => (int) $id)->all();
        $testModels = $allowedIds === []
            ? $catalog
            : $catalog->whereIn('id', $allowedIds)->values();

        return view('livewire.ai.application-test-page', [
            'testModels' => $testModels,
            'keyConfigured' => (string) config('ai.api_key') !== '' && (string) config('ai.base_url') !== '',
        ]);
    }

    private function pickDefaultModel(): void
    {
        if ($this->testModel !== '') {
            return;
        }

        $first = $this->application->models->first()
            ?: AiModel::query()->usable()->orderBy('model_identifier')->first();
        if ($first) {
            $this->testModel = $first->model_identifier;
        }
    }
}
