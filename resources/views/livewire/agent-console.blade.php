@php
    $userName = auth()->user()?->name ?? 'User';
    $userInitials = collect(preg_split('/\s+/', trim($userName)))
        ->filter()
        ->take(2)
        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
        ->implode('') ?: 'U';
    $activeTitle = null;
    if ($chatId) {
        $activeTitle = collect($chats)->firstWhere('id', $chatId)?->title;
    }
    $today = now()->startOfDay();
    $chatsToday = $chats->filter(fn ($c) => $c->updated_at && $c->updated_at->gte($today));
    $chatsOlder = $chats->filter(fn ($c) => ! $c->updated_at || $c->updated_at->lt($today));
@endphp

@push('scripts')
    <style>
        /* Palette tetap: ink gelap untuk bubble/logo, teks selalu kontras di light & dark. */
        .agent-nova {
            --ink: #17191c;
            --cloud: #f7f8f8;
            --line: #e5e8e7;
            --mint: #19a974;
            --text: #17191c;
            --text-muted: #64748b;
            --text-body: #334155;
            --surface: #ffffff;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            color: var(--text);
        }
        .dark .agent-nova {
            --cloud: #0f172a;
            --line: #334155;
            --mint: #34d399;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --text-body: #cbd5e1;
            --surface: #1e293b;
        }
        .agent-nova .scrollbar::-webkit-scrollbar { width: 5px; }
        .agent-nova .scrollbar::-webkit-scrollbar-thumb {
            background: #d8dddb;
            border-radius: 999px;
        }
        .dark .agent-nova .scrollbar::-webkit-scrollbar-thumb { background: #475569; }

        .agent-md {
            font-size: 15px;
            line-height: 1.75;
            color: var(--text-body) !important;
        }
        .agent-md > *:first-child { margin-top: 0; }
        .agent-md > *:last-child { margin-bottom: 0; }
        .agent-md p { margin: 0 0 0.65rem; color: inherit; }
        .agent-md h1, .agent-md h2, .agent-md h3 {
            font-weight: 600;
            letter-spacing: -0.02em;
            color: var(--text) !important;
            margin: 0.85rem 0 0.4rem;
            line-height: 1.3;
        }
        .agent-md h1 { font-size: 1.05rem; }
        .agent-md h2 { font-size: 1rem; }
        .agent-md h3 { font-size: 0.95rem; }
        .agent-md ul, .agent-md ol { margin: 0.35rem 0 0.75rem; padding-left: 1.15rem; }
        .agent-md ul { list-style: disc; }
        .agent-md ol { list-style: decimal; }
        .agent-md li { margin: 0.2rem 0; color: inherit; }
        .agent-md li::marker { color: var(--text-muted); }
        .agent-md strong { font-weight: 600; color: var(--text) !important; }
        .agent-md a { color: #0f766e; text-decoration: underline; text-underline-offset: 2px; }
        .dark .agent-md a { color: #6ee7b7; }
        .agent-md code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 0.85em;
            background: rgba(23,25,28,.06);
            color: var(--text) !important;
            border-radius: 0.35rem;
            padding: 0.1rem 0.35rem;
        }
        .dark .agent-md code { background: rgba(255,255,255,.1); }
        .agent-md pre {
            margin: 0.5rem 0 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 1rem;
            background: #17191c !important;
            color: #e2e8f0 !important;
            overflow-x: auto;
            font-size: 0.8rem;
            line-height: 1.55;
        }
        .agent-md pre code { background: transparent !important; padding: 0; color: #e2e8f0 !important; }
        .agent-md table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0.65rem 0 0.9rem;
            font-size: 0.82rem;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid var(--line);
            background: var(--surface);
            color: var(--text-body);
        }
        .agent-md th, .agent-md td {
            padding: 0.55rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--line);
            color: inherit;
        }
        .agent-md th {
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--text-muted) !important;
            background: var(--cloud);
        }
        .agent-md tr:last-child td { border-bottom: 0; }
        .agent-md blockquote {
            margin: 0.5rem 0;
            padding: 0.4rem 0 0.4rem 0.9rem;
            border-left: 3px solid var(--mint);
            color: var(--text-muted) !important;
        }
        .agent-md hr {
            border: 0;
            border-top: 1px solid var(--line);
            margin: 0.85rem 0;
        }

        /* Bubble user: selalu dark + teks putih (tidak ikut flip --ink di dark mode) */
        .agent-nova .agent-user-bubble {
            background: #17191c !important;
            color: #ffffff !important;
        }
        .agent-nova .agent-avatar-ink {
            background: #17191c !important;
            color: #ffffff !important;
        }
        .agent-nova .agent-send-btn {
            background: #17191c !important;
            color: #ffffff !important;
        }
        .agent-nova .agent-send-btn:hover {
            background: #19a974 !important;
            color: #ffffff !important;
        }

        @keyframes agent-dot {
            0%, 80%, 100% { opacity: .3; transform: translateY(0); }
            40% { opacity: 1; transform: translateY(-2px); }
        }
        .agent-dot {
            width: 5px; height: 5px; border-radius: 999px;
            background: #19a974;
            animation: agent-dot 1.1s ease-in-out infinite;
        }
        .dark .agent-dot { background: #34d399; }
        .agent-dot:nth-child(2) { animation-delay: .15s; }
        .agent-dot:nth-child(3) { animation-delay: .3s; }

        /* ===== Sidebar: desktop collapse + mobile drawer (pure CSS, no Tailwind rebuild) ===== */
        [x-cloak] { display: none !important; }
        .agent-sidebar {
            width: 276px;
            flex-shrink: 0;
            overflow: hidden;
            transition: width .3s ease, transform .3s ease;
        }
        .agent-sidebar-inner {
            display: flex;
            flex-direction: column;
            height: 100%;
            width: 276px;
        }
        .agent-sidebar.is-collapsed {
            width: 0;
            border-color: transparent;
        }
        .agent-backdrop {
            position: absolute;
            inset: 0;
            z-index: 20;
            background: rgba(0, 0, 0, .4);
        }
        @media (min-width: 768px) {
            .agent-backdrop { display: none !important; }
        }
        @media (max-width: 767px) {
            .agent-sidebar {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 30;
                width: 85vw;
                max-width: 320px;
                transform: translateX(-100%);
            }
            .agent-sidebar-inner { width: 85vw; max-width: 320px; }
            .agent-sidebar.is-collapsed { width: 85vw; } /* collapse is a desktop-only concept */
            .agent-sidebar.is-open { transform: translateX(0); box-shadow: 0 20px 45px rgba(0, 0, 0, .28); }
        }
        @media (max-width: 640px) {
            .agent-md table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
    <script>
        let agentForceScroll = false;

        function agentFmtDur(ms) {
            const s = Math.max(0, ms / 1000);
            if (s < 60) return s.toFixed(1) + ' dtk';
            return Math.floor(s / 60) + ' mnt ' + Math.floor(s % 60) + ' dtk';
        }
        function agentClock(d) {
            const p = n => String(n).padStart(2, '0');
            return p(d.getHours()) + ':' + p(d.getMinutes());
        }
        function agentHasSelection() {
            const s = window.getSelection ? window.getSelection().toString() : '';
            return !!s && s.length > 0;
        }
        function agentRefocus() {
            if (agentHasSelection()) return;
            const a = document.activeElement;
            if (a && (a.tagName === 'INPUT' || a.tagName === 'TEXTAREA') && a.id !== 'agent-input') return;
            const input = document.getElementById('agent-input');
            if (input && !input.disabled) input.focus();
        }
        function agentIsNearBottom(el) {
            return el.scrollHeight - el.scrollTop - el.clientHeight < 90;
        }
        function agentScrollToBottom() {
            const el = document.getElementById('agent-messages');
            if (el) el.scrollTop = el.scrollHeight;
        }
        function agentOnUserScroll() {
            const el = document.getElementById('agent-messages');
            if (el && !agentIsNearBottom(el)) agentForceScroll = false;
        }
        function agentOnSend() {
            agentForceScroll = true;
            agentScrollToBottom();
        }

        // Thinking timer — derived purely from the live element's own data-start,
        // so a mis-timed Livewire re-render can never freeze or stop it.
        let agentThinkMs = 0;
        let agentThinkActive = false;
        setInterval(() => {
            const live = document.getElementById('agent-think-elapsed');
            const note = document.getElementById('agent-think-note');
            if (live) {
                if (!live.dataset.start) {
                    live.dataset.start = String(Date.now());
                    if (note) note.textContent = ''; // clear previous "Berpikir…" note
                }
                agentThinkMs = Date.now() - Number(live.dataset.start);
                live.textContent = agentFmtDur(agentThinkMs);
                agentThinkActive = true;
            } else if (agentThinkActive) {
                agentThinkActive = false;
                if (note) note.textContent = 'Berpikir ' + agentFmtDur(agentThinkMs) + ' · ' + agentClock(new Date());
            }
        }, 100);

        function agentScrollToApproval() {
            const card = document.getElementById('agent-approval-card');
            if (card) {
                setTimeout(() => card.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 80);
            }
        }
        window.addEventListener('agent-pending', agentScrollToApproval);

        document.addEventListener('livewire:update', () => {
            if (document.getElementById('agent-approval-card')) {
                agentScrollToApproval();
            }
            if (agentHasSelection()) return;
            const el = document.getElementById('agent-messages');
            if (!el) return;
            if (agentForceScroll || agentIsNearBottom(el)) {
                setTimeout(agentScrollToBottom, 50);
            }
            setTimeout(agentRefocus, 60);
        });
        document.addEventListener('livewire:load', () => {
            setTimeout(agentScrollToBottom, 100);
            setTimeout(agentRefocus, 150);
        });
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('agent-messages');
            if (el) el.addEventListener('scroll', agentOnUserScroll, {passive: true});
        });
    </script>
@endpush

<div class="agent-nova -mx-1 sm:mx-0"
     style="height: calc(100vh - 100px)"
     x-data="{
        open: false,
        collapsed: (typeof localStorage !== 'undefined' && localStorage.getItem('agentSidebarCollapsed') === '1'),
        toggleCollapse() {
            this.collapsed = !this.collapsed;
            try { localStorage.setItem('agentSidebarCollapsed', this.collapsed ? '1' : '0'); } catch (e) {}
        },
        toggleSidebar() {
            if (window.innerWidth < 768) { this.open = !this.open; } else { this.toggleCollapse(); }
        }
     }"
     @agent-run.window="$wire.runAgent()"
     wire:poll.3s="pollUpdates">

    <div class="relative flex h-full overflow-hidden rounded-2xl border border-[color:var(--line)] bg-[color:var(--cloud)] text-[color:var(--text)]">

        {{-- Mobile drawer backdrop --}}
        <div x-show="open" x-transition.opacity @click="open = false" class="agent-backdrop md:hidden" x-cloak></div>

        {{-- ============ SIDEBAR ============ --}}
        <aside id="agent-sidebar"
               class="agent-sidebar border-r border-[color:var(--line)] bg-[color:var(--surface)]"
               :class="{ 'is-collapsed': collapsed, 'is-open': open }">
          <div class="agent-sidebar-inner">
            <div class="flex items-center justify-between px-5 py-5">
                <div class="flex items-center gap-2.5 font-semibold tracking-tight text-[color:var(--text)]">
                    <span class="agent-avatar-ink grid h-8 w-8 place-items-center rounded-xl text-sm font-bold">T</span>
                    Telixcel AI
                </div>
                <button type="button" @click="open = false" class="rounded-lg p-2 text-[color:var(--text-muted)] hover:bg-[color:var(--cloud)] hover:text-[color:var(--text)] md:hidden" aria-label="Tutup sidebar">×</button>
            </div>

            <div class="px-3">
                <button wire:click="newChat" @click="window.innerWidth < 768 && (open = false)"
                        class="flex w-full items-center gap-3 rounded-xl border border-[color:var(--line)] px-3 py-2.5 text-sm font-medium text-[color:var(--text)] hover:bg-[color:var(--cloud)] transition focus:outline-none">
                    <span class="text-lg leading-none">+</span>
                    Percakapan baru
                </button>
                <div class="relative mt-2">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    <input type="text" wire:model.debounce.300ms="search" placeholder="Cari percakapan…"
                           class="w-full rounded-xl border border-[color:var(--line)] bg-transparent py-2 pl-9 pr-3 text-sm text-[color:var(--text)] placeholder:text-[color:var(--text-muted)] focus:border-slate-400 focus:outline-none" />
                </div>
            </div>

            <nav class="scrollbar flex-1 overflow-y-auto px-3 py-6" @click="window.innerWidth < 768 && $event.target.closest('button') && (open = false)">
                @if ($chatsToday->isNotEmpty())
                    <p class="px-3 pb-3 text-[10px] font-bold uppercase tracking-[.18em] text-[color:var(--text-muted)]">Hari ini</p>
                    <div class="space-y-1 mb-6">
                        @foreach ($chatsToday as $c)
                            <div wire:key="chat-today-{{ $c->id }}" class="group flex items-center rounded-xl {{ $chatId === $c->id ? 'bg-[color:var(--cloud)]' : 'hover:bg-[color:var(--cloud)]' }}">
                                <button wire:click="loadChat({{ $c->id }})" class="flex min-w-0 flex-1 items-center gap-3 px-3 py-3 text-left text-sm focus:outline-none {{ $chatId === $c->id ? 'font-medium text-[color:var(--text)]' : 'text-[color:var(--text-body)]' }}">
                                    <span class="text-[color:var(--text-muted)]">◌</span>
                                    <span class="truncate">{{ $c->title ?: 'Tanpa judul' }}</span>
                                </button>
                                <button wire:click="confirmDeleteChat({{ $c->id }})" class="mr-2 rounded-lg p-1.5 text-[color:var(--text-muted)] opacity-0 transition hover:text-red-500 group-hover:opacity-100" title="Hapus">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($chatsOlder->isNotEmpty())
                    <p class="px-3 pb-3 text-[10px] font-bold uppercase tracking-[.18em] text-[color:var(--text-muted)]">{{ $chatsToday->isNotEmpty() ? 'Sebelumnya' : 'Riwayat' }}</p>
                    <div class="space-y-1">
                        @foreach ($chatsOlder as $c)
                            <div wire:key="chat-old-{{ $c->id }}" class="group flex items-center rounded-xl {{ $chatId === $c->id ? 'bg-[color:var(--cloud)]' : 'hover:bg-[color:var(--cloud)]' }}">
                                <button wire:click="loadChat({{ $c->id }})" class="flex min-w-0 flex-1 items-center gap-3 px-3 py-3 text-left text-sm focus:outline-none {{ $chatId === $c->id ? 'font-medium text-[color:var(--text)]' : 'text-[color:var(--text-body)]' }}">
                                    <span class="text-[color:var(--text-muted)]">◌</span>
                                    <span class="truncate">{{ $c->title ?: 'Tanpa judul' }}</span>
                                </button>
                                <button wire:click="confirmDeleteChat({{ $c->id }})" class="mr-2 rounded-lg p-1.5 text-[color:var(--text-muted)] opacity-0 transition hover:text-red-500 group-hover:opacity-100" title="Hapus">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($chats->isEmpty())
                    <p class="px-3 py-10 text-center text-sm text-[color:var(--text-muted)]">
                        {{ $search ? 'Tidak ada percakapan.' : 'Belum ada riwayat.' }}
                    </p>
                @endif
            </nav>

            <div class="border-t border-[color:var(--line)] p-3">
                <div class="flex w-full items-center gap-3 rounded-xl px-3 py-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-[#dceee7] text-xs font-bold text-[#0f766e]">{{ $userInitials }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-[color:var(--text)]">{{ $userName }}</span>
                        <span class="block truncate text-xs text-[color:var(--text-muted)]">Telixcel Console</span>
                    </span>
                </div>
            </div>
          </div>
        </aside>

        {{-- ============ MAIN ============ --}}
        <main class="flex min-w-0 flex-1 flex-col bg-[color:var(--cloud)]">
            <header class="flex h-[68px] items-center justify-between border-b border-[color:var(--line)] bg-[color:var(--surface)]/90 px-5 backdrop-blur md:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" @click="toggleSidebar()"
                            class="rounded-lg p-2 text-[color:var(--text-muted)] hover:bg-[color:var(--cloud)] hover:text-[color:var(--text)]"
                            :aria-label="collapsed ? 'Buka sidebar' : 'Tutup sidebar'" title="Sidebar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="4" width="18" height="16" rx="2" />
                            <path d="M9 4v16" stroke-linecap="round" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-[color:var(--text)]">{{ $activeTitle ?: 'Percakapan baru' }}</p>
                        <p class="text-xs text-[color:var(--text-muted)]">
                            Telixcel AI
                            @if ($isThinking)
                                · <span class="text-[color:var(--mint)]">thinking…</span>
                            @else
                                · Online
                            @endif
                        </p>
                    </div>
                </div>
                <button wire:click="newChat" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-[color:var(--text-muted)] hover:bg-[color:var(--cloud)] hover:text-[color:var(--text)] focus:outline-none">
                    Baru
                </button>
            </header>

            <section id="agent-messages" class="scrollbar flex-1 overflow-y-auto">
                <div class="mx-auto max-w-3xl px-5 py-10 md:px-8">
                    @foreach ($messages as $idx => $m)
                        @if ($m['role'] === 'user')
                            <div class="mb-10 flex items-start justify-end gap-4" wire:key="msg-{{ $idx }}-u">
                                <div class="agent-user-bubble max-w-xl rounded-2xl rounded-tr-sm px-5 py-4 text-[15px] leading-7">
                                    <div class="whitespace-pre-wrap break-words">{{ $m['content'] }}</div>
                                </div>
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#dceee7] text-xs font-bold text-[#0f766e]">{{ $userInitials }}</span>
                            </div>
                        @else
                            <div class="mb-10 flex items-start gap-4 group" wire:key="msg-{{ $idx }}-a" x-data="{ copied:false }">
                                <span class="agent-avatar-ink grid h-9 w-9 shrink-0 place-items-center rounded-xl text-xs font-bold">T</span>
                                <div class="max-w-2xl min-w-0 flex-1">
                                    <div class="mb-1 flex items-center gap-2">
                                        <span class="text-sm font-semibold text-[color:var(--text)]">Telixcel AI</span>
                                        <span class="text-xs text-[color:var(--text-muted)]">AI</span>
                                    </div>
                                    <div class="agent-md" x-ref="body">
                                        {!! $this->format($m['content']) !!}
                                    </div>
                                    <div class="mt-4 flex gap-1">
                                        <button type="button"
                                                @click="navigator.clipboard.writeText($refs.body.innerText); copied=true; setTimeout(()=>copied=false,1500)"
                                                class="rounded-lg p-2 text-[color:var(--text-muted)] hover:bg-[color:var(--surface)] hover:text-[color:var(--text)] focus:outline-none"
                                                :title="copied ? 'Tersalin' : 'Salin'">
                                            <svg x-show="!copied" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            <svg x-show="copied" x-cloak class="h-4 w-4 text-[color:var(--mint)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if ($isThinking)
                        <div class="mb-10 flex items-start gap-4" id="agent-think-live">
                            <span class="agent-avatar-ink grid h-9 w-9 shrink-0 place-items-center rounded-xl text-xs font-bold">T</span>
                            <div class="max-w-2xl">
                                <div class="mb-1 flex items-center gap-2">
                                    <span class="text-sm font-semibold text-[color:var(--text)]">Telixcel AI</span>
                                    <span class="text-xs text-[color:var(--text-muted)]">thinking</span>
                                </div>
                                <div class="flex items-center gap-2.5 text-[15px] text-[color:var(--text-muted)]">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="agent-dot"></span>
                                        <span class="agent-dot"></span>
                                        <span class="agent-dot"></span>
                                    </span>
                                    <span id="agent-think-elapsed" wire:ignore class="font-mono text-xs tabular-nums text-[color:var(--mint)]">0.0 dtk</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <p id="agent-think-note" wire:ignore class="text-xs text-[color:var(--text-muted)] empty:hidden pl-[3.25rem]"></p>
                </div>
            </section>

            {{-- Composer dock --}}
            <div class="bg-gradient-to-t from-[color:var(--cloud)] via-[color:var(--cloud)] to-transparent px-5 pb-5 pt-4 md:px-8">
                <div class="mx-auto max-w-3xl space-y-3">

                    @if ($pendingAction)
                        <div id="agent-approval-card" wire:key="agent-approval-{{ implode('-', $pendingAction['ids'] ?? []) }}-{{ md5(json_encode($pendingAction['values'] ?? [])) }}"
                             class="rounded-2xl border-2 border-amber-400 bg-amber-50 dark:bg-amber-950/30 p-4 shadow-sm ring-2 ring-amber-400/30">
                            <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-300">Konfirmasi perubahan</p>
                            <p class="mt-1 text-sm font-semibold text-[color:var(--text)]">{{ $pendingAction['summary'] ?? 'Perubahan diajukan' }}</p>
                            <div class="mt-3 max-h-36 space-y-2 overflow-y-auto scrollbar">
                                @foreach (($pendingAction['diff'] ?? []) as $row)
                                    <div class="rounded-xl border border-[color:var(--line)] bg-[color:var(--cloud)] px-3 py-2">
                                        <p class="text-xs font-medium text-[color:var(--text-muted)]">{{ $row['label'] }}</p>
                                        @if ($pendingAction['type'] === 'update')
                                            @foreach ($row['after'] as $field => $newValue)
                                                <p class="mt-0.5 text-sm text-[color:var(--text-body)]">
                                                    <span class="font-mono text-xs text-[color:var(--text-muted)]">{{ $field }}</span>
                                                    <s class="text-red-500">{{ $row['before'][$field] ?? '—' }}</s>
                                                    →
                                                    <span class="font-semibold text-[color:var(--mint)]">{{ $newValue }}</span>
                                                </p>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3 flex gap-2">
                                <button wire:click="approvePendingAction" wire:loading.attr="disabled"
                                        class="agent-send-btn rounded-xl px-4 py-2 text-sm font-medium transition focus:outline-none">
                                    Terapkan
                                </button>
                                <button wire:click="rejectPendingAction"
                                        class="rounded-xl border border-[color:var(--line)] px-4 py-2 text-sm font-medium text-[color:var(--text)] hover:bg-[color:var(--cloud)] focus:outline-none">
                                    Batalkan
                                </button>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="send" @submit="agentOnSend()">
                        <div class="flex items-end gap-3 rounded-2xl border border-[color:var(--line)] bg-[color:var(--surface)] p-2 shadow-[0_8px_30px_rgba(23,25,28,.06)] focus-within:border-slate-400">
                            <button type="button" class="mb-0.5 rounded-xl p-3 text-xl leading-none text-[color:var(--text-muted)] hover:bg-[color:var(--cloud)] hover:text-[color:var(--text)]" aria-hidden="true" tabindex="-1">+</button>
                            <input type="text" id="agent-input" wire:model.defer="input" :disabled="$wire.isThinking"
                                   autocomplete="off" autofocus
                                   @keydown.enter.shift.prevent
                                   class="min-h-12 flex-1 border-0 bg-transparent px-1 py-3 text-sm text-[color:var(--text)] outline-none ring-0 focus:ring-0 placeholder:text-[color:var(--text-muted)] disabled:opacity-60"
                                   placeholder="Tulis pesan untuk Telixcel AI…" />
                            <button type="submit" wire:loading.attr="disabled" wire:target="send,runAgent" :disabled="$wire.isThinking"
                                    class="agent-send-btn grid h-11 w-11 shrink-0 place-items-center rounded-xl text-lg transition disabled:opacity-40 focus:outline-none"
                                    aria-label="Kirim pesan">
                                ↑
                            </button>
                        </div>
                        <p class="mt-1.5 text-center text-[9px] leading-snug text-[color:var(--text-muted)] opacity-70">
                            AI dapat keliru · cek info penting · update status butuh konfirmasi
                        </p>
                    </form>
                </div>
            </div>
        </main>
    </div>

    @if ($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-[2px]" wire:click="cancelDeleteChat">
            <div class="w-full max-w-sm mx-4 rounded-2xl border border-[color:var(--line)] bg-[color:var(--surface)] p-6 shadow-xl" wire:click.stop>
                <h3 class="text-sm font-semibold text-[color:var(--text)]">Hapus percakapan?</h3>
                <p class="mt-1 text-sm leading-6 text-[color:var(--text-muted)]">
                    Hapus <span class="font-medium text-[color:var(--text)]">"{{ Str::limit($deleteTargetTitle, 40) }}"</span>? Semua pesan akan hilang.
                </p>
                <div class="mt-5 flex justify-end gap-2">
                    <button wire:click="cancelDeleteChat" class="rounded-xl border border-[color:var(--line)] px-4 py-2 text-sm font-medium text-[color:var(--text)] hover:bg-[color:var(--cloud)]">Batal</button>
                    <button wire:click="executeDeleteChat" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
