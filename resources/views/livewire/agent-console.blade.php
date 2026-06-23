@push('scripts')
    <style>
        .agent-md p { margin-bottom: 0.5rem; }
        .agent-md p:last-child { margin-bottom: 0; }
        .agent-md ul { list-style: disc; padding-left: 1.25rem; margin: 0.25rem 0 0.5rem; }
        .agent-md ol { list-style: decimal; padding-left: 1.25rem; margin: 0.25rem 0 0.5rem; }
        .agent-md li { margin-bottom: 0.15rem; }
        .agent-md strong { font-weight: 600; }
        .agent-md code { background: rgba(0,0,0,.08); border-radius: 3px; padding: 0 4px; font-size: .85em; }
        .agent-md table { width: 100%; border-collapse: collapse; margin: 0.5rem 0; font-size: .9em; }
        .agent-md th, .agent-md td { border: 1px solid rgba(0,0,0,.12); padding: 4px 8px; text-align: left; }
    </style>
    <script>
        // True while the user is selecting/highlighting text (so we don't disrupt copy).
        function agentHasSelection() {
            const s = window.getSelection ? window.getSelection().toString() : '';
            return !!s && s.length > 0;
        }
        function agentRefocus() {
            if (agentHasSelection()) return; // never steal focus mid text-selection
            const a = document.activeElement;
            // Don't steal focus while the user is typing in another field (e.g. the search box).
            if (a && (a.tagName === 'INPUT' || a.tagName === 'TEXTAREA') && a.id !== 'agent-input') return;
            const input = document.getElementById('agent-input');
            if (input && !input.disabled) input.focus();
        }
        document.addEventListener('livewire:update', () => {
            // Background polls (e.g. notifications every few seconds) trigger this too —
            // if the user is selecting text to copy, don't scroll or refocus (it clears the selection).
            if (agentHasSelection()) return;
            const el = document.getElementById('agent-messages');
            if (el) setTimeout(() => { el.scrollTop = el.scrollHeight; }, 50);
            setTimeout(agentRefocus, 60);
        });
        document.addEventListener('livewire:load', () => setTimeout(agentRefocus, 150));
    </script>
@endpush

<div class="w-full px-3 py-2" x-data @agent-run.window="$wire.runAgent()">
    <div class="flex bg-white dark:bg-slate-800 shadow rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700" style="height: calc(100vh - 110px)">

        {{-- ============ SIDEBAR ============ --}}
        <aside class="hidden md:flex md:flex-col w-64 flex-shrink-0 bg-gray-50 dark:bg-slate-900/40 border-r border-gray-100 dark:border-slate-700">
            <div class="p-3 space-y-2">
                {{-- New chat --}}
                <button wire:click="newChat"
                        class="w-full inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-600 transition focus:outline-none"
                        style="border-radius:9999px">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                    New chat
                </button>

                {{-- Search chats --}}
                <div class="relative">
                    <svg class="h-4 w-4 absolute left-3 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    <input type="text" wire:model.debounce.300ms="search" placeholder="Search chats"
                           class="w-full pl-9 pr-3 py-2 text-sm bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-400"
                           style="border-radius:0.5rem" />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-2 pb-2">
                <p class="px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-gray-400">Recents</p>
                @forelse ($chats as $c)
                    <div wire:key="chat-{{ $c->id }}"
                         class="group flex items-center {{ $chatId === $c->id ? 'bg-gray-200/70 dark:bg-slate-700' : 'hover:bg-gray-100 dark:hover:bg-slate-700/50' }}"
                         style="border-radius:0.5rem">
                        <button wire:click="loadChat({{ $c->id }})" class="flex-1 min-w-0 text-left px-3 py-2 focus:outline-none">
                            <div class="text-sm truncate {{ $chatId === $c->id ? 'font-medium text-gray-800 dark:text-white' : 'text-gray-600 dark:text-slate-300' }}">
                                {{ $c->title ?: 'Untitled' }}
                            </div>
                        </button>
                        <button wire:click="confirmDeleteChat({{ $c->id }})"
                                class="opacity-0 group-hover:opacity-100 text-gray-300 hover:text-red-600 px-2 py-2 focus:outline-none transition" title="Hapus">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-6">{{ $search ? 'Tidak ditemukan.' : 'Belum ada riwayat.' }}</p>
                @endforelse
            </div>
        </aside>

        {{-- ============ MAIN CHAT ============ --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto" id="agent-messages">
                <div class="max-w-3xl mx-auto px-4 py-6 space-y-6">
                    @foreach ($messages as $m)
                        @if ($m['role'] === 'user')
                            <div class="flex justify-end">
                                <div class="bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-100 px-4 py-2.5 text-sm leading-relaxed max-w-xl"
                                     style="border-radius:1.25rem">
                                    {{ $m['content'] }}
                                </div>
                            </div>
                        @else
                            <div class="flex gap-3 group" x-data="{ copied:false }">
                                <span class="flex-shrink-0 flex items-center justify-center h-7 w-7 rounded-full bg-blue-600 text-white text-[11px] font-semibold">AI</span>
                                <div class="min-w-0 flex-1">
                                    <div class="agent-md text-sm leading-relaxed text-gray-800 dark:text-slate-100" x-ref="body">
                                        {!! $this->format($m['content']) !!}
                                    </div>
                                    <button @click="navigator.clipboard.writeText($refs.body.innerText); copied=true; setTimeout(()=>copied=false,1500)"
                                            class="mt-1.5 inline-flex items-center gap-1 text-[11px] text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 opacity-0 group-hover:opacity-100 transition focus:outline-none">
                                        <svg x-show="!copied" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <svg x-show="copied" x-cloak class="h-3.5 w-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if ($isThinking)
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 flex items-center justify-center h-7 w-7 rounded-full bg-blue-600 text-white text-[11px] font-semibold">AI</span>
                            <div class="text-gray-400 text-sm italic pt-1">Mengetik…</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Approval card (ubah status) --}}
            @if ($pendingAction)
                <div class="max-w-3xl mx-auto w-full px-4">
                    <div class="mb-3 border border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 p-4" style="border-radius:0.75rem">
                        <div class="flex items-center gap-2 font-semibold text-yellow-700 dark:text-yellow-300 mb-2">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            <span>Konfirmasi: {{ $pendingAction['summary'] }}</span>
                        </div>
                        <div class="text-sm space-y-2 max-h-64 overflow-y-auto">
                            @foreach ($pendingAction['diff'] as $row)
                                <div class="border-b border-yellow-200 dark:border-yellow-700 pb-1">
                                    <span class="font-mono text-xs text-gray-600 dark:text-slate-300">{{ $row['label'] }}</span>
                                    @if ($pendingAction['type'] === 'update')
                                        @foreach ($row['after'] as $field => $newValue)
                                            <div class="text-gray-700 dark:text-slate-200">
                                                <span class="font-medium">{{ $field }}:</span>
                                                <s class="text-red-500">{{ $row['before'][$field] ?? '—' }}</s>
                                                <span class="mx-1">→</span>
                                                <b class="text-green-600 dark:text-green-400">{{ $newValue }}</b>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button wire:click="approvePendingAction" wire:loading.attr="disabled"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-1.5 rounded font-medium">Terapkan</button>
                            <button wire:click="rejectPendingAction"
                                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm px-4 py-1.5 rounded font-medium">Batalkan</button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Input (ChatGPT-style pill) --}}
            <div class="px-4 pb-3 pt-1">
                <form wire:submit.prevent="send" class="max-w-3xl mx-auto">
                    <div class="flex items-center gap-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 px-3 py-1.5 shadow-sm"
                         style="border-radius:9999px">
                        <input type="text" id="agent-input" wire:model.defer="input" :disabled="$wire.isThinking"
                               autocomplete="off" autofocus
                               class="flex-1 border-0 bg-transparent text-sm text-gray-800 dark:text-white focus:ring-0 focus:outline-none"
                               placeholder="Ask anything" />
                        <button type="submit" wire:loading.attr="disabled" :disabled="$wire.isThinking"
                                class="flex-shrink-0 flex items-center justify-center h-8 w-8 rounded-full bg-blue-600 hover:bg-blue-700 disabled:opacity-40 text-white focus:outline-none">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5M5 12l7-7 7 7"/></svg>
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2 text-center">
                        AI bisa keliru — periksa info penting. · Ubah status perlu konfirmasi · Tidak bisa buat / hapus data
                    </p>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete confirmation overlay --}}
    @if ($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click="cancelDeleteChat">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-sm mx-4 p-6 border border-gray-200 dark:border-slate-600" wire:click.stop>
                <h3 class="text-base font-semibold text-gray-800 dark:text-slate-100 mb-1">Hapus chat?</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-5">
                    Hapus <span class="font-medium text-gray-700 dark:text-slate-200">"{{ Str::limit($deleteTargetTitle, 40) }}"</span>? Semua pesan akan hilang.
                </p>
                <div class="flex justify-end gap-2">
                    <button wire:click="cancelDeleteChat"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-slate-300 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition">
                        Batal
                    </button>
                    <button wire:click="executeDeleteChat"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
