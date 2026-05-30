<?php

namespace App\Http\Livewire;

use App\Services\Agent\ApprovalExecutor;
use App\Services\Agent\OllamaAgentService;
use Illuminate\Support\Str;
use Livewire\Component;

class AgentConsole extends Component
{
    /** @var array<int,array{role:string,content:string}> */
    public array $messages = [];

    public string $input = '';

    /** @var array|null Pending UPDATE/DELETE proposal awaiting approval. */
    public ?array $pendingAction = null;

    public bool $isThinking = false;

    public function mount(): void
    {
        abort_unless($this->isAdmin(), 403);

        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Hi! Ask me to show, create, update, or delete records '
                . '(orders, projects, tickets, blast messages). '
                . 'Example: "show orders with status draft".',
        ];
    }

    /** Step 1: capture input + show the thinking indicator, then trigger step 2 in the browser. */
    public function send(): void
    {
        $text = trim($this->input);
        if ($text === '') {
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $text];
        $this->input = '';
        $this->pendingAction = null;
        $this->isThinking = true;

        $this->dispatchBrowserEvent('agent-run');
    }

    /** Step 2: the actual (blocking) LLM call. */
    public function runAgent(OllamaAgentService $agent): void
    {
        abort_unless($this->isAdmin(), 403);

        $lastUser = collect($this->messages)
            ->last(fn ($m) => $m['role'] === 'user')['content'] ?? '';

        if ($lastUser === '') {
            $this->isThinking = false;

            return;
        }

        try {
            $result = $agent->run($this->historyForModel(), $lastUser);
            $this->messages[] = ['role' => 'assistant', 'content' => $result['reply']];
            $this->pendingAction = $result['pending'];
        } catch (\Throwable $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Error talking to the AI service: ' . $e->getMessage(),
            ];
        } finally {
            $this->isThinking = false;
        }
    }

    public function approvePendingAction(ApprovalExecutor $executor): void
    {
        abort_unless($this->isAdmin(), 403);

        if (! $this->pendingAction) {
            return;
        }

        $outcome = $executor->apply($this->pendingAction);
        $this->messages[] = ['role' => 'assistant', 'content' => $outcome['message']];
        $this->pendingAction = null;
    }

    public function rejectPendingAction(): void
    {
        $this->pendingAction = null;
        $this->messages[] = ['role' => 'assistant', 'content' => 'Action cancelled. Nothing was changed.'];
    }

    /** History for the model: prior user/assistant turns, excluding the current trailing user message. */
    private function historyForModel(): array
    {
        return collect($this->messages)
            ->map(fn ($m) => ['role' => $m['role'], 'content' => $m['content']])
            ->slice(0, -1)
            ->values()
            ->all();
    }

    /**
     * Render an assistant message (Markdown) to safe HTML, server-side.
     * Done on the server so Livewire re-renders never wipe chat history.
     */
    public function format(string $text): string
    {
        return Str::markdown($text, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
    }

    private function isAdmin(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->super->first()?->role === 'superadmin') {
            return true;
        }

        return $user->activeRole
            && str_contains($user->activeRole->role->name ?? '', 'Admin');
    }

    public function render()
    {
        return view('livewire.agent-console');
    }
}
