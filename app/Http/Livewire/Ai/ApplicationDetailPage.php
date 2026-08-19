<?php

namespace App\Http\Livewire\Ai;

use App\Exceptions\AiGatewayException;
use App\Http\Livewire\Ai\Concerns\AuthorizesAiAdmin;
use App\Models\AiApplication;
use App\Models\AiModel;
use App\Services\Ai\AiAuditService;
use App\Services\Ai\AiModelService;
use App\Services\Ai\AiPermissionService;
use App\Services\Ai\AiUpstreamService;
use Illuminate\Support\Str;
use Laravel\Jetstream\ConfirmsPasswords;
use Livewire\Component;

class ApplicationDetailPage extends Component
{
    use AuthorizesAiAdmin;
    use ConfirmsPasswords;

    public AiApplication $application;
    public $plainApiKey = null;
    public $keyRevealError = null;
    public $input = [];
    public $selectedModels = [];
    public $modelsError = null;
    public $modelsStatus = null;

    public function mount(AiApplication $application): void
    {
        $this->authorizeAiAdmin();
        $this->application = $application->load('models');
        $this->plainApiKey = session('ai_plain_api_key');
        $this->fillForm();
    }

    public function refreshModels(AiUpstreamService $upstream, AiModelService $models): void
    {
        $this->recheckModels($upstream, $models);
    }

    public function recheckModels(AiUpstreamService $upstream, AiModelService $models): void
    {
        $this->authorizeAiAdmin();
        $this->modelsError = null;
        $this->modelsStatus = null;
        $this->syncVerifiedModels($upstream, $models, true);
    }

    private function syncVerifiedModels(AiUpstreamService $upstream, AiModelService $models, bool $force): void
    {
        if ((string) config('ai.base_url') === '' || (string) config('ai.api_key') === '') {
            $this->modelsError = 'Set AI_BASE_URL and AI_API_KEY first.';

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
            $report = $models->verifyUsable($upstream, $ids, $force);
            $usable = (int) ($report['ok'] ?? 0);
            $this->modelsStatus = $usable === 1
                ? '1 model yang dapat digunakan'
                : $usable.' model yang dapat digunakan';
        } catch (AiGatewayException $e) {
            $this->modelsError = $e->getMessage();
        }
    }

    public function rules(): array
    {
        return [
            'input.name' => 'required|string|max:191',
            'input.slug' => 'required|alpha_dash|max:80|unique:ai_applications,slug,'.$this->application->id,
            'input.status' => 'required|in:active,inactive',
            'input.rate_limit_per_minute' => 'nullable|integer|min:0',
            'input.monthly_cost_limit' => 'nullable|numeric|min:0',
            'input.cost_currency' => 'required|in:USD,IDR',
            'input.require_end_user' => 'boolean',
            'selectedModels' => 'array',
        ];
    }

    public function save(AiAuditService $audit, AiPermissionService $permissions): void
    {
        $this->authorizeAiAdmin();
        if (! isset($this->input['require_end_user'])) {
            $this->input['require_end_user'] = false;
        }
        $this->validate();

        $data = [
            'name' => $this->input['name'],
            'slug' => Str::slug($this->input['slug']),
            'status' => $this->input['status'],
            'rate_limit_per_minute' => $this->nullableInt($this->input['rate_limit_per_minute'] ?? null),
            'monthly_cost_limit' => $this->nullableNumber($this->input['monthly_cost_limit'] ?? null),
            'cost_currency' => strtoupper((string) ($this->input['cost_currency'] ?? 'USD')) === 'IDR' ? 'IDR' : 'USD',
            'require_end_user' => (bool) $this->input['require_end_user'],
        ];

        $before = $this->application->only(array_keys($data));
        $this->application->fill($data)->save();
        $selected = collect($this->selectedModels)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        $this->application->models()->sync(
            AiModel::query()->usable()->whereIn('id', $selected)->pluck('id')->all()
        );
        $this->application->load('models');
        $permissions->forget($this->application);
        $audit->record('AiApplication', $this->application->id, 'updated by '.$this->actorLabel(), $before);

        session()->flash('ai_saved', true);
    }

    public function revealKey(): void
    {
        $this->authorizeAiAdmin();
        $this->ensurePasswordIsConfirmed();
        $this->keyRevealError = null;
        $this->plainApiKey = $this->application->revealedKey();

        if (! $this->plainApiKey) {
            $this->keyRevealError = __('This key was issued before it could be stored. Regenerate the key to view it. The old key will stop working immediately.');
        }
    }

    public function regenerateKey(AiAuditService $audit): void
    {
        $this->authorizeAiAdmin();
        $this->ensurePasswordIsConfirmed();
        $this->keyRevealError = null;
        $this->plainApiKey = $this->application->issueKey();
        $this->application->save();
        $audit->record('AiApplication', $this->application->id, 'api key regenerated by '.$this->actorLabel());
    }

    public function toggleStatus(AiAuditService $audit): void
    {
        $this->authorizeAiAdmin();
        $before = ['status' => $this->application->status];
        $this->application->status = $this->application->isActive()
            ? AiApplication::STATUS_INACTIVE
            : AiApplication::STATUS_ACTIVE;
        $this->application->save();
        $this->input['status'] = $this->application->status;
        $audit->record('AiApplication', $this->application->id, 'status changed by '.$this->actorLabel(), $before);
    }

    public function dismissKey(): void
    {
        $this->plainApiKey = null;
        $this->keyRevealError = null;
    }

    public function getClientEndpointProperty(): string
    {
        return rtrim((string) config('app.url'), '/');
    }

    public function getCompletionsUrlProperty(): string
    {
        return url('/api/v1/ai/chat/completions');
    }

    public function getModelsUrlProperty(): string
    {
        return url('/api/v1/ai/models');
    }

    public function render()
    {
        $catalog = AiModel::query()->usable()->orderBy('model_identifier')->get();

        return view('livewire.ai.application-detail-page', [
            'catalog' => $catalog,
        ]);
    }

    private function fillForm(): void
    {
        $this->input = [
            'name' => $this->application->name,
            'slug' => $this->application->slug,
            'status' => $this->application->status,
            'rate_limit_per_minute' => $this->application->rate_limit_per_minute,
            'monthly_cost_limit' => $this->application->monthly_cost_limit,
            'cost_currency' => $this->application->costCurrency(),
            'require_end_user' => $this->application->require_end_user,
        ];
        $this->selectedModels = $this->application->models->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    private function nullableInt($value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }

    private function nullableNumber($value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }
}
