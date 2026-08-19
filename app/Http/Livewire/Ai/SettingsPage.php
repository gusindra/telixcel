<?php

namespace App\Http\Livewire\Ai;

use App\Http\Livewire\Ai\Concerns\AuthorizesAiAdmin;
use App\Models\AiSetting;
use App\Services\Ai\AiAuditService;
use Livewire\Component;

class SettingsPage extends Component
{
    use AuthorizesAiAdmin;

    public $base_url = '';
    public $api_key = '';

    public function mount(): void
    {
        $this->authorizeAiAdmin();
        $setting = AiSetting::stored();
        $this->base_url = (string) ($setting->base_url ?? '');
        $this->api_key = (string) ($setting->api_key ?? '');
    }

    public function rules(): array
    {
        return [
            'base_url' => ['nullable', 'string', 'max:500', 'starts_with:http://,https://'],
            'api_key' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function save(AiAuditService $audit): void
    {
        $this->authorizeAiAdmin();
        $this->validate();

        $base = $this->normalizeUrl($this->base_url);
        $key = trim((string) $this->api_key) !== '' ? trim((string) $this->api_key) : null;

        $setting = AiSetting::stored();
        $before = $setting ? $setting->only(['base_url', 'api_key']) : null;
        $setting = $setting ?: new AiSetting();

        $setting->base_url = $base;
        $setting->api_key = $key;
        $setting->save();

        if ($base !== null) {
            config(['ai.base_url' => $base]);
            config(['ai.endpoint' => $base.'/chat/completions']);
        }
        if ($key !== null) {
            config(['ai.api_key' => $key]);
        }

        $audit->record('AiSetting', $setting->id, 'upstream endpoint updated by '.$this->actorLabel(), $before);

        session()->flash('ai_saved', true);
    }

    public function render()
    {
        $stored = AiSetting::stored();

        return view('livewire.ai.settings-page', [
            'effectiveBaseUrl' => (string) config('ai.base_url'),
            'fromDb' => (bool) ($stored->base_url ?? false),
        ]);
    }

    private function normalizeUrl($url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        return rtrim($url, '/');
    }
}
