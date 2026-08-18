<?php

namespace App\Http\Livewire\Ai;

use App\Http\Livewire\Ai\Concerns\AuthorizesAiAdmin;
use App\Http\Livewire\Ai\Concerns\RendersAiUsageGraph;
use App\Models\AiApplication;
use Livewire\Component;

class UsagePage extends Component
{
    use AuthorizesAiAdmin;
    use RendersAiUsageGraph;

    public $application = null;

    public function mount($application = null): void
    {
        $this->authorizeAiAdmin();
        $this->application = $application instanceof AiApplication ? $application : null;
    }

    public function render()
    {
        return $this->renderAiUsage();
    }

    protected function scopedApplication(): ?AiApplication
    {
        return $this->application instanceof AiApplication && $this->application->exists
            ? $this->application
            : null;
    }
}
