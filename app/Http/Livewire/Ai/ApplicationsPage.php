<?php

namespace App\Http\Livewire\Ai;

use App\Http\Livewire\Ai\Concerns\AuthorizesAiAdmin;
use App\Models\AiApplication;
use App\Services\Ai\AiAuditService;
use Illuminate\Support\Str;
use Livewire\Component;

class ApplicationsPage extends Component
{
    use AuthorizesAiAdmin;

    public $modalVisible = false;
    public $input = [];

    protected $listeners = [
        'aiAppToggle' => 'toggleStatus',
    ];

    public function mount(): void
    {
        $this->authorizeAiAdmin();
        $this->resetForm();
    }

    public function rules(): array
    {
        return [
            'input.name' => 'required|string|max:191',
        ];
    }

    public function actionShowModal(): void
    {
        $this->resetForm();
        $this->modalVisible = true;
    }

    public function save(AiAuditService $audit)
    {
        $this->authorizeAiAdmin();
        $this->validate();

        $app = new AiApplication([
            'name' => $this->input['name'],
            'slug' => $this->uniqueSlug($this->input['name']),
            'status' => AiApplication::STATUS_ACTIVE,
            'rate_limit_per_minute' => 60,
            'monthly_token_limit' => null,
            'monthly_cost_limit' => null,
            'cost_currency' => 'USD',
            'require_end_user' => true,
        ]);
        $plainApiKey = $app->issueKey();
        $app->save();
        $audit->record('AiApplication', $app->id, 'created by '.$this->actorLabel());

        session()->flash('ai_plain_api_key', $plainApiKey);

        return redirect()->route('ai.applications.show', $app);
    }

    public function toggleStatus(int $id, AiAuditService $audit): void
    {
        $this->authorizeAiAdmin();
        $app = AiApplication::findOrFail($id);
        $before = ['status' => $app->status];
        $app->status = $app->isActive() ? AiApplication::STATUS_INACTIVE : AiApplication::STATUS_ACTIVE;
        $app->save();
        $audit->record('AiApplication', $app->id, 'status changed by '.$this->actorLabel(), $before);
        $this->emit('refreshLivewireDatatable');
    }

    public function resetForm(): void
    {
        $this->input = ['name' => ''];
    }

    public function render()
    {
        return view('livewire.ai.applications-page');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'app';
        $slug = $base;
        $i = 2;
        while (AiApplication::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
