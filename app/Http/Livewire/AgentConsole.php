<?php

namespace App\Http\Livewire;

use App\Models\AgentChat;
use App\Models\AgentChatMessage;
use App\Services\Agent\ApprovalExecutor;
use App\Services\Agent\OllamaAgentService;
use Illuminate\Support\Str;
use Livewire\Component;

class AgentConsole extends Component
{
    /** @var array<int,array{role:string,content:string}> */
    public array $messages = [];

    public string $input = '';

    /** Search box to filter the chat history list. */
    public string $search = '';

    /** Current chat session id (null = unsaved new chat showing the welcome only). */
    public ?int $chatId = null;

    /** @var array|null Pending UPDATE/DELETE proposal awaiting approval. */
    public ?array $pendingAction = null;

    public bool $isThinking = false;

    public bool $confirmingDelete = false;
    public ?int $deleteTargetId = null;
    public string $deleteTargetTitle = '';

    public function mount(): void
    {
        abort_unless($this->isAdmin(), 403);

        // Restore the most recent session so a refresh keeps the conversation.
        $latest = AgentChat::where('user_id', auth()->id())->orderByDesc('updated_at')->first();

        if ($latest) {
            $this->loadChat($latest->id);
        } else {
            $this->newChat();
        }
    }

    /** Start a fresh (unsaved) chat: only the welcome message is shown. */
    public function newChat(): void
    {
        $this->chatId = null;
        $this->pendingAction = null;
        $this->isThinking = false;
        $this->input = '';
        $this->messages = [$this->welcomeMessage()];
    }

    /** Load a past session's messages into the view. */
    public function loadChat(int $id): void
    {
        $chat = AgentChat::where('user_id', auth()->id())->find($id);
        if (! $chat) {
            return;
        }

        $this->chatId = $chat->id;
        $this->pendingAction = null;
        $this->isThinking = false;
        $this->messages = $chat->messages
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->all();

        if (empty($this->messages)) {
            $this->messages = [$this->welcomeMessage()];
        }
    }

    /** Show the delete confirmation UI for a chat. */
    public function confirmDeleteChat(int $id): void
    {
        $chat = AgentChat::where('user_id', auth()->id())->find($id);
        if (! $chat) {
            return;
        }

        $this->deleteTargetId = $chat->id;
        $this->deleteTargetTitle = $chat->title ?: 'Untitled';
        $this->confirmingDelete = true;
    }

    /** Dismiss the delete confirmation. */
    public function cancelDeleteChat(): void
    {
        $this->confirmingDelete = false;
        $this->deleteTargetId = null;
        $this->deleteTargetTitle = '';
    }

    /** Execute the confirmed deletion. */
    public function executeDeleteChat(): void
    {
        if (! $this->deleteTargetId) {
            return;
        }

        $this->deleteChat($this->deleteTargetId);
        $this->confirmingDelete = false;
        $this->deleteTargetId = null;
        $this->deleteTargetTitle = '';
    }

    /** Delete a session and its messages. */
    public function deleteChat(int $id): void
    {
        $chat = AgentChat::where('user_id', auth()->id())->find($id);
        if (! $chat) {
            return;
        }

        AgentChatMessage::where('agent_chat_id', $chat->id)->delete();
        $chat->delete();

        if ($this->chatId === $id) {
            $this->newChat();
        }
    }

    /** Step 1: capture input + show the thinking indicator, then trigger step 2 in the browser. */
    public function send(): void
    {
        abort_unless($this->isAdmin(), 403);

        $text = trim($this->input);
        if ($text === '') {
            return;
        }

        // Create the session on the first message (title from the question).
        if (! $this->chatId) {
            $chat = AgentChat::create([
                'user_id' => auth()->id(),
                'title'   => Str::limit($text, 50),
            ]);
            $this->chatId = $chat->id;
        }

        $this->messages[] = ['role' => 'user', 'content' => $text];
        $this->persistMessage('user', $text);

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
            $this->persistMessage('assistant', $result['reply']);
            $this->pendingAction = $result['pending'];
        } catch (\Throwable $e) {
            $reply = 'Gagal menghubungi AI service: ' . $e->getMessage();
            $this->messages[] = ['role' => 'assistant', 'content' => $reply];
            $this->persistMessage('assistant', $reply);
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
        $this->persistMessage('assistant', $outcome['message']);
        $this->pendingAction = null;
    }

    public function rejectPendingAction(): void
    {
        $this->pendingAction = null;
        $message = 'Dibatalkan. Tidak ada yang berubah.';
        $this->messages[] = ['role' => 'assistant', 'content' => $message];
        $this->persistMessage('assistant', $message);
    }

    /** Save a message to the current session and bump its updated_at. */
    private function persistMessage(string $role, string $content): void
    {
        if (! $this->chatId) {
            return;
        }

        AgentChatMessage::create([
            'agent_chat_id' => $this->chatId,
            'role'          => $role,
            'content'       => $content,
        ]);

        // Bump session so it sorts to the top of the history list.
        AgentChat::where('id', $this->chatId)->update(['updated_at' => now()]);
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

    private function welcomeMessage(): array
    {
        return [
            'role' => 'assistant',
            'content' => "Halo! Saya analis data AI untuk **Telixcel**.\n\n"
                . "Saya bisa membantu **analisa & laporan data**. Contoh:\n"
                . "- *Project mana yang task-nya masih berjalan?*\n"
                . "- *Kontrak yang akan expired bulan ini*\n"
                . "- *Ringkasan task per status untuk project tertentu*\n"
                . "- *Order dengan status unpaid minggu ini*\n"
                . "- *Task yang sudah melewati target date*\n\n"
                . "Saya juga bisa **ubah status** record — tapi perlu konfirmasi Anda dulu sebelum dieksekusi.\n\n"
                . "Saya **tidak bisa** membuat atau menghapus data.",
        ];
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
        $chats = AgentChat::where('user_id', auth()->id())
            ->when($this->search !== '', fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->orderByDesc('updated_at')
            ->get(['id', 'title', 'updated_at']);

        return view('livewire.agent-console', ['chats' => $chats]);
    }
}
