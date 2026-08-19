@php
    $userName = auth()->user()?->name ?? 'User';
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
        .agent-nova {
            flex: 1 1 auto;
            min-height: 0;
            height: 100%;
            --ac-bg: var(--tx-surface);
            --ac-rail: #f4f6fb;
            --ac-line: var(--tx-border);
            --ac-text: var(--tx-fg);
            --ac-muted: var(--tx-fg-muted);
            --ac-subtle: var(--tx-fg-subtle);
            --ac-blue: var(--tx-primary);
            --ac-blue-soft: var(--tx-active);
            color: var(--ac-text);
        }
        .agent-nova .scrollbar::-webkit-scrollbar { width: 6px; }
        .agent-nova .scrollbar::-webkit-scrollbar-thumb {
            background: var(--ac-line); border-radius: 999px;
        }

        .agent-shell {
            display: flex; height: 100%; overflow: hidden;
            background: var(--ac-bg); border: 1px solid var(--ac-line); border-radius: 12px;
        }

        .agent-sidebar {
            width: 272px; flex: none; overflow: hidden;
            background: var(--ac-rail); border-right: 1px solid var(--ac-line);
            transition: width .2s ease, transform .2s ease;
        }
        .agent-sidebar-inner { display: flex; flex-direction: column; height: 100%; width: 272px; min-width: 0; overflow: hidden; }
        .agent-sidebar.is-collapsed { width: 0; border-right-color: transparent; }
        .agent-backdrop { position: absolute; inset: 0; z-index: 20; background: rgba(15,23,42,.28); }

        .agent-brand { padding: 16px 16px 8px; }
        .agent-brand-name { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
        .agent-brand-sub { margin: 2px 0 0; font-size: 11px; color: var(--ac-subtle); }

        .agent-side-actions { display: flex; flex-direction: column; gap: 8px; padding: 8px 12px 12px; }
        .agent-new {
            display: flex; align-items: center; gap: 8px; height: 36px; padding: 0 12px;
            border: 1px solid var(--ac-line); border-radius: 8px;
            background: var(--ac-bg); color: var(--ac-text);
            font: inherit; font-size: 13px; font-weight: 600; cursor: pointer;
        }
        .agent-new:hover { border-color: var(--tx-border-strong); }
        .agent-search {
            width: 100%; height: 32px; padding: 0 12px;
            border: 1px solid var(--ac-line); border-radius: 8px;
            background: var(--ac-bg); color: var(--ac-text); font-size: 13px;
        }

        .agent-sec {
            margin: 8px 12px 4px; font-size: 11px; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase; color: var(--ac-subtle);
        }
        .agent-chat-item { min-width: 0; }
        .agent-row {
            display: flex; align-items: center; gap: 8px;
            flex: 1 1 auto; min-width: 0; min-height: 36px; padding: 8px 8px 8px 10px; border: 0; border-radius: 8px;
            background: transparent; color: var(--ac-text); text-align: left;
            font: inherit; font-size: 13px; cursor: pointer;
        }
        .agent-row:hover { background: rgba(0,90,194,.05); }
        .agent-row.is-on { background: var(--ac-blue-soft); color: var(--ac-blue); font-weight: 600; }
        .agent-dot-item {
            width: 6px; height: 6px; border-radius: 99px; flex: none; background: var(--tx-border-strong);
        }
        .agent-row.is-on .agent-dot-item { background: var(--ac-blue); }
        .agent-del {
            flex: none; width: 24px; height: 24px; padding: 0; border: 0; border-radius: 6px;
            background: transparent; color: var(--ac-subtle); cursor: pointer; opacity: 0;
        }
        .agent-chat-item:hover .agent-del { opacity: 1; }
        .agent-del:hover { color: #be123c; background: #fff1f3; }
        .agent-foot {
            padding: 12px 16px; border-top: 1px solid var(--ac-line);
            font-size: 12px; color: var(--ac-muted);
        }
        .agent-foot b { display: block; font-size: 12px; font-weight: 650; color: var(--ac-text); }

        .agent-main { display: flex; flex-direction: column; min-width: 0; flex: 1; background: var(--ac-bg); }
        .agent-head {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            height: 56px; padding: 0 16px; border-bottom: 1px solid var(--ac-line); flex: none;
        }
        .agent-head-id { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .agent-head-mark {
            width: 32px; height: 32px; border-radius: 8px; flex: none;
            display: grid; place-items: center; border: 0; padding: 0; cursor: pointer;
            background: transparent; color: var(--ac-muted);
        }
        .agent-head-mark:hover { background: var(--tx-hover); color: var(--ac-blue); }
        .agent-head-mark .material-symbols-outlined { font-size: 18px; font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24; }
        .agent-head h2 { margin: 0; font-size: 14px; font-weight: 700; line-height: 1.2; }
        .agent-head p { margin: 2px 0 0; font-size: 12px; color: var(--ac-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .agent-head-actions { display: flex; align-items: center; gap: 4px; flex: none; }

        .agent-thread { flex: 1; overflow-y: auto; }
        .agent-col { width: 100%; max-width: 920px; margin: 0 auto; padding: 20px 16px 16px; }
        .agent-turn { margin: 0 0 16px; }
        .agent-turn.is-user { display: flex; justify-content: flex-end; }
        .agent-turn.is-ai { display: flex; justify-content: flex-start; }
        .agent-user {
            max-width: min(72%, 640px);
            padding: 8px 12px; border-radius: 16px;
            background: #eef3fb; color: var(--ac-text);
            font-size: 14px; line-height: 1.5; word-break: break-word; white-space: pre-wrap;
        }
        .agent-ai { width: 100%; max-width: 100%; min-width: 0; }

        .agent-md { font-size: 14px; line-height: 1.55; color: var(--ac-text); }
        .agent-md > *:first-child { margin-top: 0; }
        .agent-md > *:last-child { margin-bottom: 0; }
        .agent-md p { margin: 0 0 8px; }
        .agent-md h1, .agent-md h2, .agent-md h3 {
            font-weight: 700; letter-spacing: -.015em; color: var(--ac-text);
            margin: 16px 0 8px; line-height: 1.3;
        }
        .agent-md h1 { font-size: 16px; }
        .agent-md h2 { font-size: 15px; }
        .agent-md h3 { font-size: 14px; }
        .agent-md ul, .agent-md ol { margin: 0 0 8px; padding-left: 20px; }
        .agent-md li { margin: 4px 0; }
        .agent-md li::marker { color: var(--ac-subtle); }
        .agent-md strong { font-weight: 700; }
        .agent-md a { color: var(--ac-blue); text-decoration: underline; text-underline-offset: 2px; }
        .agent-md code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px; background: #eef3fb; color: #0a4ea1;
            border-radius: 4px; padding: 1px 6px;
        }
        .agent-md pre {
            margin: 8px 0 12px; padding: 12px; border-radius: 8px;
            background: #0f172a; color: #e2e8f0; overflow-x: auto; font-size: 12px; line-height: 1.5;
        }
        .agent-md pre code { background: transparent; padding: 0; color: inherit; }
        .agent-md table {
            width: 100%; border-collapse: collapse; margin: 8px 0 12px; font-size: 13px;
            border: 1px solid var(--ac-line); border-radius: 8px; overflow: hidden;
        }
        .agent-md th, .agent-md td { padding: 8px 12px; text-align: left; border-bottom: 1px solid var(--ac-line); }
        .agent-md th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--ac-muted); background: var(--ac-rail); }
        .agent-md tr:last-child td { border-bottom: 0; }
        .agent-md blockquote { margin: 8px 0; padding: 4px 0 4px 12px; border-left: 3px solid var(--ac-blue); color: var(--ac-muted); }
        .agent-copy {
            margin-top: 4px; height: 28px; padding: 0 8px; border: 0; border-radius: 6px;
            background: transparent; color: var(--ac-subtle); font-size: 12px; cursor: pointer;
            opacity: 0;
        }
        .agent-turn.is-ai:hover .agent-copy { opacity: 1; }

        .agent-think {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 12px; border-radius: 8px;
            background: var(--ac-blue-soft); color: var(--ac-blue);
            font-size: 13px; font-weight: 600;
        }
        .agent-think-note { margin: 8px 0 0; font-size: 11px; color: var(--ac-subtle); }

        .agent-tool {
            border: 1px solid var(--ac-line); border-radius: 12px;
            background: var(--ac-bg); padding: 12px;
        }
        .agent-tool-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
        .agent-badge {
            display: inline-flex; align-items: center; height: 20px; padding: 0 8px;
            border-radius: 999px; background: var(--ac-blue-soft); color: var(--ac-blue);
            font-size: 11px; font-weight: 700; letter-spacing: .02em;
        }
        .agent-tool h3 { margin: 0; font-size: 13px; font-weight: 700; }
        .agent-tool-row {
            margin-top: 8px; padding: 8px 12px; border: 1px solid var(--ac-line);
            border-radius: 8px; background: var(--ac-rail); font-size: 13px;
        }
        .agent-tool-actions { display: flex; gap: 8px; margin-top: 12px; }

        .agent-dock { flex: none; padding: 12px 16px 16px; }
        .agent-composer {
            border: 1px solid var(--ac-line); border-radius: 16px;
            background: #fff; box-shadow: 0 1px 2px rgba(15,23,42,.06);
            padding: 8px 8px 8px 12px;
        }
        .agent-composer-row { display: flex; align-items: flex-end; gap: 8px; }
        .agent-composer input {
            flex: 1; min-height: 36px; border: 0; background: transparent;
            color: var(--ac-text); font: inherit; font-size: 14px; outline: none; box-shadow: none;
        }
        .agent-send {
            width: 32px; height: 32px; padding: 0; border: 0; border-radius: 8px;
            background: var(--ac-blue); color: var(--tx-on-primary); cursor: pointer; flex: none;
            display: grid; place-items: center;
        }
        .agent-send:hover { background: var(--tx-primary-hover); }
        .agent-send:disabled { opacity: .4; cursor: default; }
        .agent-composer-meta {
            display: flex; align-items: center; gap: 8px;
            padding: 4px 4px 0; font-size: 11px; color: var(--ac-subtle);
        }
        .agent-disclaimer {
            margin: 8px 0 0; text-align: center; font-size: 10px; line-height: 1.4; color: var(--ac-subtle);
        }

        [x-cloak] { display: none !important; }
        .agent-head-burger { display: none !important; }
        @media (min-width: 768px) { .agent-backdrop { display: none !important; } }
        @media (max-width: 767px) {
            .agent-head-burger { display: inline-flex !important; }
        }
        @media (max-width: 767px) {
            .agent-sidebar {
                position: absolute; top: 0; bottom: 0; left: 0; z-index: 30;
                width: min(280px, 86vw); transform: translateX(-105%);
            }
            .agent-sidebar-inner { width: min(280px, 86vw); }
            .agent-sidebar.is-collapsed { width: min(280px, 86vw); }
            .agent-sidebar.is-open { transform: translateX(0); box-shadow: 8px 0 24px rgba(15,23,42,.12); }
            .agent-col, .agent-dock { padding-left: 12px; padding-right: 12px; }
            .agent-user { max-width: 86%; }
            .agent-copy { opacity: 1; }
        }
        @media (max-width: 640px) {
            .agent-md table { display: block; overflow-x: auto; }
            .agent-head { padding: 0 12px; }
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

        let agentThinkMs = 0;
        let agentThinkActive = false;
        setInterval(() => {
            const live = document.getElementById('agent-think-elapsed');
            const note = document.getElementById('agent-think-note');
            if (live) {
                if (!live.dataset.start) {
                    live.dataset.start = String(Date.now());
                    if (note) note.textContent = '';
                }
                agentThinkMs = Date.now() - Number(live.dataset.start);
                live.textContent = agentFmtDur(agentThinkMs);
                agentThinkActive = true;
            } else if (agentThinkActive) {
                agentThinkActive = false;
                if (note) note.textContent = agentFmtDur(agentThinkMs) + ' · ' + agentClock(new Date());
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

<div class="agent-nova"
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
     @@agent-run.window="$wire.runAgent()"
     wire:poll.3s="pollUpdates">

    <div class="agent-shell relative">
        <div x-show="open" x-transition.opacity @click="open = false" class="agent-backdrop md:hidden" x-cloak></div>

        <aside id="agent-sidebar" class="agent-sidebar" :class="{ 'is-collapsed': collapsed, 'is-open': open }">
            <div class="agent-sidebar-inner">
                <div class="agent-brand">
                    <p class="agent-brand-name">{{ __('Chats') }}</p>
                </div>

                <div class="agent-side-actions">
                    <button type="button" class="agent-new" wire:click="newChat" @click="window.innerWidth < 768 && (open = false)">
                        <span class="material-symbols-outlined" style="font-size:18px;">add</span>
                        {{ __('Percakapan baru') }}
                    </button>
                    <input type="search" wire:model.debounce.300ms="search" class="agent-search" placeholder="{{ __('Cari percakapan…') }}" aria-label="{{ __('Cari percakapan') }}">
                </div>

                <nav class="scrollbar flex-1 overflow-y-auto overflow-x-hidden px-2 pb-2" @click="window.innerWidth < 768 && $event.target.closest('[data-chat]') && (open = false)">
                    @if ($chatsToday->isNotEmpty())
                        <p class="agent-sec">{{ __('Recent') }}</p>
                        @foreach ($chatsToday as $c)
                            <div wire:key="chat-today-{{ $c->id }}" class="agent-chat-item flex items-center" data-chat>
                                <button type="button" wire:click="loadChat({{ $c->id }})" class="agent-row {{ $chatId === $c->id ? 'is-on' : '' }}">
                                    <span class="agent-dot-item"></span>
                                    <span class="min-w-0 flex-1 truncate">{{ $c->title ?: __('Untitled') }}</span>
                                </button>
                                <button type="button" wire:click="confirmDeleteChat({{ $c->id }})" class="agent-del" title="{{ __('Delete') }}">
                                    <span class="material-symbols-outlined" style="font-size:16px;">close</span>
                                </button>
                            </div>
                        @endforeach
                    @endif

                    @if ($chatsOlder->isNotEmpty())
                        <p class="agent-sec">{{ __('History') }}</p>
                        @foreach ($chatsOlder as $c)
                            <div wire:key="chat-old-{{ $c->id }}" class="agent-chat-item flex items-center" data-chat>
                                <button type="button" wire:click="loadChat({{ $c->id }})" class="agent-row {{ $chatId === $c->id ? 'is-on' : '' }}">
                                    <span class="agent-dot-item"></span>
                                    <span class="min-w-0 flex-1 truncate">{{ $c->title ?: __('Untitled') }}</span>
                                </button>
                                <button type="button" wire:click="confirmDeleteChat({{ $c->id }})" class="agent-del" title="{{ __('Delete') }}">
                                    <span class="material-symbols-outlined" style="font-size:16px;">close</span>
                                </button>
                            </div>
                        @endforeach
                    @endif

                    @if ($chats->isEmpty())
                        <p class="px-3 py-8 text-center text-sm" style="color: var(--ac-subtle);">
                            {{ $search ? __('No conversations.') : __('No history yet.') }}
                        </p>
                    @endif
                </nav>

                <div class="agent-foot">
                    <b>{{ $userName }}</b>
                </div>
            </div>
        </aside>

        <main class="agent-main">
            <header class="agent-head">
                <div class="agent-head-id">
                    <button type="button" @click="toggleSidebar()" class="agent-head-burger tx-iconbtn h-8 w-8 rounded-lg" title="{{ __('Sidebar') }}">
                        <span class="material-symbols-outlined" style="font-size:18px;">menu</span>
                    </button>
                    <button type="button" class="agent-head-mark" @click="toggleSidebar()" title="{{ __('Sidebar') }}">
                        <span class="material-symbols-outlined">view_sidebar</span>
                    </button>
                    <div class="min-w-0">
                        <h2>{{ $activeTitle ?: __('New conversation') }}</h2>
                    </div>
                </div>
                <div class="agent-head-actions">
                    <button type="button" class="tx-btn tx-btn-ghost" wire:click="newChat">{{ __('New') }}</button>
                </div>
            </header>

            <section id="agent-messages" class="agent-thread scrollbar">
                <div class="agent-col">
                    @foreach ($messages as $idx => $m)
                        @if ($m['role'] === 'user')
                            <div class="agent-turn is-user" wire:key="msg-{{ $idx }}-u">
                                <div class="agent-user">{{ $m['content'] }}</div>
                            </div>
                        @else
                            <div class="agent-turn is-ai" wire:key="msg-{{ $idx }}-a" x-data="{ copied:false }">
                                <div class="agent-ai">
                                    <div class="agent-md" x-ref="body">
                                        {!! $this->format($m['content']) !!}
                                    </div>
                                    <button type="button"
                                            class="agent-copy"
                                            @click="navigator.clipboard.writeText($refs.body.innerText); copied=true; setTimeout(()=>copied=false,1500)"
                                            :title="copied ? '{{ __('Copied') }}' : '{{ __('Copy') }}'">
                                        <span class="material-symbols-outlined" style="font-size:16px;vertical-align:-3px;" x-text="copied ? 'check' : 'content_copy'">content_copy</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if ($isThinking)
                        <div class="agent-turn is-ai" id="agent-think-live">
                            <div class="agent-think">
                                {{ __('Thinking...') }}
                                <span id="agent-think-elapsed" wire:ignore class="font-mono text-xs tabular-nums" style="font-weight:500;">0.0 dtk</span>
                            </div>
                        </div>
                    @endif

                    <p id="agent-think-note" wire:ignore class="agent-think-note empty:hidden"></p>
                </div>
            </section>

            <div class="agent-dock">
                <div class="agent-col" style="padding-top: 0; padding-bottom: 0;">
                    @if ($pendingAction)
                        <div id="agent-approval-card" class="agent-tool" style="margin-bottom: 12px;"
                             wire:key="agent-approval-{{ implode('-', $pendingAction['ids'] ?? []) }}-{{ md5(json_encode($pendingAction['values'] ?? [])) }}">
                            <h3>{{ __('Konfirmasi perubahan') }}</h3>
                            <p class="mt-1 mb-0 text-sm" style="color: var(--ac-muted);">{{ $pendingAction['summary'] ?? __('Change proposed') }}</p>
                            <div class="max-h-36 overflow-y-auto scrollbar">
                                @foreach (($pendingAction['diff'] ?? []) as $row)
                                    <div class="agent-tool-row">
                                        <p class="m-0 text-xs font-medium" style="color: var(--ac-muted);">{{ $row['label'] }}</p>
                                        @if ($pendingAction['type'] === 'update')
                                            @foreach ($row['after'] as $field => $newValue)
                                                <p class="mt-1 mb-0">
                                                    <code>{{ $field }}</code>
                                                    <s style="color:#be123c;">{{ $row['before'][$field] ?? '—' }}</s>
                                                    →
                                                    <strong>{{ $newValue }}</strong>
                                                </p>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="agent-tool-actions">
                                <button type="button" wire:click="approvePendingAction" wire:loading.attr="disabled" class="tx-btn">{{ __('Apply') }}</button>
                                <button type="button" wire:click="rejectPendingAction" class="tx-btn tx-btn-ghost">{{ __('Cancel') }}</button>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="send" @submit="agentOnSend()">
                        <div class="agent-composer">
                            <div class="agent-composer-row">
                                <input type="text" id="agent-input" wire:model.defer="input" :disabled="$wire.isThinking"
                                       autocomplete="off" autofocus
                                       @keydown.enter.shift.prevent
                                       placeholder="Write a message..." />
                                <button type="submit" class="agent-send" wire:loading.attr="disabled" wire:target="send,runAgent" :disabled="$wire.isThinking" aria-label="{{ __('Send') }}">
                                    <span class="material-symbols-outlined" style="font-size:18px;">arrow_upward</span>
                                </button>
                            </div>
                        </div>
                        <p class="agent-disclaimer">{{ __('AI can be wrong. Verify important data. Status updates need confirmation.') }}</p>
                    </form>
                </div>
            </div>
        </main>
    </div>

    @if ($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click="cancelDeleteChat">
            <div class="w-full max-w-sm mx-4 rounded-xl border p-5" style="background: var(--ac-bg); border-color: var(--ac-line);" wire:click.stop>
                <h3 class="text-sm font-semibold m-0">{{ __('Delete conversation?') }}</h3>
                <p class="mt-2 mb-0 text-sm" style="color: var(--ac-muted);">
                    {{ __('Delete') }} <span class="font-medium" style="color: var(--ac-text);">"{{ \Illuminate\Support\Str::limit($deleteTargetTitle, 40) }}"</span>?
                </p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="cancelDeleteChat" class="tx-btn tx-btn-ghost">{{ __('Cancel') }}</button>
                    <button type="button" wire:click="executeDeleteChat" class="tx-btn tx-btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
