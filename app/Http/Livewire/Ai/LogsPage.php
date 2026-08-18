<?php

namespace App\Http\Livewire\Ai;

use App\Http\Livewire\Ai\Concerns\AuthorizesAiAdmin;
use App\Models\LogChange;
use Livewire\Component;
use Livewire\WithPagination;

class LogsPage extends Component
{
    use AuthorizesAiAdmin;
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        $this->authorizeAiAdmin();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $audits = LogChange::query()
            ->where('model', 'like', 'Ai%')
            ->latest('id')
            ->when($this->search !== '', function ($q) {
                $s = $this->search;
                $q->where(function ($q) use ($s) {
                    $q->where('model', 'like', "%{$s}%")
                        ->orWhere('remark', 'like', "%{$s}%");
                });
            })
            ->paginate(15);

        return view('livewire.ai.logs-page', [
            'audits' => $audits,
        ]);
    }
}
