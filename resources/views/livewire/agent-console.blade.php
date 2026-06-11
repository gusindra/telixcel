@push('scripts')
    <style>
        .agent-md p { margin-bottom: 0.5rem; }
        .agent-md p:last-child { margin-bottom: 0; }
        .agent-md ul { list-style: disc; padding-left: 1.25rem; margin: 0.25rem 0 0.5rem; }
        .agent-md ol { list-style: decimal; padding-left: 1.25rem; margin: 0.25rem 0 0.5rem; }
        .agent-md li { margin-bottom: 0.15rem; }
        .agent-md strong { font-weight: 600; }
        .agent-md code { background: rgba(0,0,0,.08); border-radius: 3px; padding: 0 4px; font-size: .85em; }
    </style>
    <script>
        function agentRefocus() {
            const input = document.getElementById('agent-input');
            if (input && !input.disabled) input.focus();
        }
        document.addEventListener('livewire:update', () => {
            const el = document.getElementById('agent-messages');
            if (el) setTimeout(() => { el.scrollTop = el.scrollHeight; }, 50);
            setTimeout(agentRefocus, 60);
        });
        document.addEventListener('livewire:load', () => setTimeout(agentRefocus, 100));
    </script>
@endpush

<div class="max-w-3xl mx-auto px-4 py-6" x-data @agent-run.window="$wire.runAgent()">
    <div class="bg-white dark:bg-slate-700 shadow rounded-lg overflow-hidden">

        {{-- Messages --}}
        <div class="p-4 space-y-3 h-[60vh] overflow-y-auto" id="agent-messages">
            @foreach ($messages as $m)
                <div class="flex {{ $m['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="rounded-lg px-3 py-2 max-w-[80%] text-sm leading-relaxed
                        {{ $m['role'] === 'user'
                            ? 'bg-orange-500 text-white'
                            : 'agent-md bg-gray-100 text-gray-800 dark:bg-slate-600 dark:text-slate-100' }}">
                        @if ($m['role'] === 'user')
                            {{ $m['content'] }}
                        @else
                            {!! $this->format($m['content']) !!}
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($isThinking)
                <div class="flex justify-start">
                    <div class="rounded-lg px-3 py-2 bg-gray-100 dark:bg-slate-600 text-gray-400 text-sm italic">
                        Mengetik…
                    </div>
                </div>
            @endif
        </div>

        {{-- Approval card untuk UPDATE / DELETE --}}
        @if ($pendingAction)
            <div class="mx-4 mb-4 border border-amber-400 bg-amber-50 dark:bg-amber-900/30 rounded-lg p-4">
                <div class="flex items-center gap-2 font-semibold text-amber-800 dark:text-amber-200 mb-2">
                    <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <span>Konfirmasi: {{ $pendingAction['summary'] }}</span>
                </div>

                <div class="text-sm space-y-2 max-h-64 overflow-y-auto">
                    @foreach ($pendingAction['diff'] as $row)
                        <div class="border-b border-amber-200 dark:border-amber-700 pb-1">
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
                            class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-4 py-1.5 rounded font-medium">
                        Terapkan
                    </button>
                    <button wire:click="rejectPendingAction"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm px-4 py-1.5 rounded font-medium">
                        Batalkan
                    </button>
                </div>
            </div>
        @endif

        {{-- Input --}}
        <form wire:submit.prevent="send" class="border-t border-gray-200 dark:border-slate-600 p-3 flex gap-2">
            <input type="text" id="agent-input" wire:model.defer="input" :disabled="$wire.isThinking"
                   autocomplete="off" autofocus
                   class="flex-1 border-gray-300 dark:border-slate-500 dark:bg-slate-800 dark:text-white rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Contoh: project yg task-nya berjalan, kontrak expired bulan ini..." />
            <button type="submit" wire:loading.attr="disabled" :disabled="$wire.isThinking"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm px-5 py-2 rounded-md">
                Kirim
            </button>
        </form>
    </div>

    <p class="text-xs text-gray-400 mt-2 text-center">
        Model: {{ config('services.ollama.model') }} · Ubah status memerlukan konfirmasi · Tidak bisa buat / hapus data
    </p>
</div>
