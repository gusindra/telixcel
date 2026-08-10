<?php

namespace App\Http\Livewire;

use App\Models\AgentChat;
use App\Models\AgentChatMessage;
use App\Models\Report;
use App\Services\Agent\AgentRunner;
use App\Services\Agent\ApprovalExecutor;
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

    /** Report ids already announced in chat this session (avoid duplicate notices). */
    public array $notifiedReportIds = [];

    public function mount(): void
    {
        abort_unless($this->isAdmin(), 403);

        // Warm AI backend (Hermes) while the page loads.
        AgentRunner::maybeWarm();

        // Restore the most recent session so a refresh keeps the conversation.
        $latest = $this->ownedChatsQuery()->orderByDesc('updated_at')->first();

        if ($latest) {
            $this->loadChat($latest->id);
        } else {
            $this->newChat();
        }
    }

    /**
     * Guard against Livewire public-property tampering of chatId.
     * Foreign session IDs are discarded so user A never binds to user B's chat.
     */
    public function updatedChatId($value): void
    {
        if ($value === null || $value === '') {
            $this->chatId = null;

            return;
        }

        if (! $this->ownedChat((int) $value)) {
            $this->chatId = null;
            $this->pendingAction = null;
            $this->isThinking = false;
            $this->messages = [$this->welcomeMessage()];
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
        $chat = $this->ownedChat($id);
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
        $chat = $this->ownedChat($id);
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
        $chat = $this->ownedChat($id);
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

        // Re-verify ownership before writing (public chatId can be tampered with).
        if ($this->chatId && ! $this->ownedChat($this->chatId)) {
            $this->chatId = null;
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

    /** Step 2: the actual (blocking) agent call — Hermes profile telixcel by default. */
    public function runAgent(AgentRunner $agent): void
    {
        abort_unless($this->isAdmin(), 403);

        $lastUser = collect($this->messages)
            ->last(fn ($m) => $m['role'] === 'user')['content'] ?? '';

        if ($lastUser === '') {
            $this->isThinking = false;

            return;
        }

        try {
            // Pass chatId so Hermes conversation / session-key stay per-user + per-console session.
            $result = $agent->run($this->historyForModel(), $lastUser, $this->chatId);
            $this->messages[] = ['role' => 'assistant', 'content' => $result['reply']];
            $this->persistMessage('assistant', $result['reply']);
            // Only show approval card when THIS turn returned a real update proposal.
            // Do not pull stale PendingActionStore on list/read replies.
            $this->pendingAction = $result['pending'] ?? null;

            if (is_array($this->pendingAction) && empty($this->pendingAction['ids'])) {
                $this->pendingAction = null;
            }
            if ($this->pendingAction) {
                $this->dispatchBrowserEvent('agent-pending');
            }
        } catch (\Throwable $e) {
            $reply = 'Gagal menghubungi AI service: ' . $e->getMessage();
            $this->messages[] = ['role' => 'assistant', 'content' => $reply];
            $this->persistMessage('assistant', $reply);
            // Still surface a late proposal if the store was written before the error.
            $this->pendingAction = \App\Services\Agent\PendingActionStore::get(auth()->id());
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
        \App\Services\Agent\PendingActionStore::forget(auth()->id());
    }

    public function rejectPendingAction(): void
    {
        $this->pendingAction = null;
        \App\Services\Agent\PendingActionStore::forget(auth()->id());
        $message = 'Dibatalkan. Tidak ada yang berubah.';
        $this->messages[] = ['role' => 'assistant', 'content' => $message];
        $this->persistMessage('assistant', $message);
    }

    /** Save a message to the current session and bump its updated_at. */
    private function persistMessage(string $role, string $content): void
    {
        $chat = $this->ownedChat();
        if (! $chat) {
            // Drop a tampered foreign chatId so subsequent writes open a new session.
            $this->chatId = null;

            return;
        }

        AgentChatMessage::create([
            'agent_chat_id' => $chat->id,
            'role'          => $role,
            'content'       => $content,
        ]);

        // Bump session so it sorts to the top of the history list.
        $chat->forceFill(['updated_at' => now()])->save();
    }

    /**
     * Fetch a chat owned by the authenticated user (null when missing or foreign).
     * Always scope by user_id so sessions never cross users.
     */
    private function ownedChat(?int $id = null): ?AgentChat
    {
        $id = $id ?? $this->chatId;
        if (! $id || ! auth()->id()) {
            return null;
        }

        return $this->ownedChatsQuery()->find($id);
    }

    /** Base query for the current user's chat sessions only. */
    private function ownedChatsQuery()
    {
        return AgentChat::where('user_id', auth()->id());
    }

    /** History for the model: prior user/assistant turns, excluding the current trailing user message. */
    private function historyForModel(): array
    {
        return collect($this->messages)
            ->map(fn ($m) => ['role' => $m['role'], 'content' => (string) ($m['content'] ?? '')])
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
                . "- *Task yang sudah melewati target date*\n"
                . "- *Buat laporan bulanan (PDF)*\n\n"
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

    /** Single poll entry (Livewire honours one wire:poll per component). */
    public function pollUpdates(): void
    {
        $this->checkPendingAction();
        $this->checkReportStatus();
    }

    /**
     * Polled — surfaces approval card when ToolExecutor proposed an UPDATE
     * (PendingActionStore). Handles proposals that land after runAgent returns.
     */
    public function checkPendingAction(): void
    {
        if ($this->pendingAction) {
            return; // already showing one
        }

        $pending = \App\Services\Agent\PendingActionStore::get(auth()->id());
        if ($pending) {
            $this->pendingAction = $pending;
            $this->dispatchBrowserEvent('agent-pending');
        }
    }

    /** Polled — drops a one-time "report ready" message (with download link) into the chat. */
    public function checkReportStatus(): void
    {
        $report = Report::where('user_id', auth()->id())
            ->where('status', 'ready')
            ->when($this->notifiedReportIds, fn ($q) => $q->whereNotIn('id', $this->notifiedReportIds))
            ->where('created_at', '>=', now()->subHour())
            ->orderByDesc('created_at')
            ->first();

        if (! $report) {
            return;
        }

        $this->notifiedReportIds[] = $report->id;

        // Don't repeat the notice if this chat already shows the link (e.g. after a reload).
        $downloadPath = "/reports/{$report->id}/download";
        if (collect($this->messages)->contains(fn ($m) => str_contains($m['content'] ?? '', $downloadPath))) {
            return;
        }

        $message = "✅ Laporan sudah siap!\n\n"
            . "**{$report->label()}**\n\n"
            . '[Download PDF](' . url($downloadPath) . ')';
        $this->messages[] = ['role' => 'assistant', 'content' => $message];
        $this->persistMessage('assistant', $message);
    }

    public function render()
    {
        $chats = $this->ownedChatsQuery()
            ->when($this->search !== '', fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->orderByDesc('updated_at')
            ->get(['id', 'title', 'updated_at']);

        return view('livewire.agent-console', ['chats' => $chats]);
    }
}
