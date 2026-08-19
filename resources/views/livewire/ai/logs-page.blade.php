@php
    $kv = static function (?array $data): string {
        if (! is_array($data) || $data === []) {
            return '';
        }
        $out = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $v = json_encode($v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } elseif ($v === null) {
                $v = '—';
            } else {
                $v = (string) $v;
            }
            $out .= '<div class="flex gap-2 py-0.5"><dt class="font-mono text-slate-500 w-28 shrink-0">'.e($k).'</dt><dd class="font-mono text-slate-800 break-all m-0">'.e($v).'</dd></div>';
        }

        return $out;
    };
@endphp

<div class="tx-stack">
    <x-page-section :title="__('AI Activity Log')">
        <x-slot name="toolbar">
            <div class="flex flex-wrap items-center gap-2">
                <input type="search" wire:model.debounce.300ms="search" placeholder="{{ __('Search model or remark…') }}"
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-slate-400 focus:outline-none" />

                @if($search !== '')
                    <button type="button" wire:click="clearFilters"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100">
                        {{ __('Reset') }}
                    </button>
                @endif

                <button type="button" wire:click="$refresh" title="{{ __('Refresh') }}"
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100">
                    {{ __('Refresh') }}
                </button>
            </div>
        </x-slot>

        @if($audits->isEmpty())
            <div class="py-12 text-center">
                <span class="material-symbols-outlined text-4xl text-slate-300">verified_user</span>
                <p class="m-0 mt-2 text-sm text-slate-500">{{ __('No audit entries yet.') }}</p>
            </div>
        @else
            <div class="space-y-2.5">
                @foreach($audits as $audit)
                    <article class="rounded-xl border border-slate-200 bg-white shadow-sm" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex w-full items-center gap-3 px-4 py-3 text-left">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                                <span class="material-symbols-outlined text-lg">tune</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium text-slate-900">{{ $audit->remark ?: '—' }}</span>
                                <span class="block text-xs text-slate-500">
                                    {{ $audit->created_at?->format('d M Y, H:i') }} · <span class="font-mono">{{ $audit->model }} #{{ $audit->model_id }}</span>
                                </span>
                            </span>
                            @if(! empty($audit->before))
                                <span class="shrink-0 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''">
                                    <span class="material-symbols-outlined text-lg">expand_more</span>
                                </span>
                            @endif
                        </button>

                        @if(! empty($audit->before))
                            <div x-show="open" x-cloak class="border-t border-slate-100 px-4 py-3">
                                <p class="m-0 mb-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Before') }}</p>
                                <dl class="m-0 rounded-lg bg-slate-50 p-3">
                                    @php $beforeKv = $kv(is_array($audit->before) ? $audit->before : null); @endphp
                                    @if($beforeKv !== '')
                                        {!! $beforeKv !!}
                                    @else
                                        <p class="m-0 text-xs text-slate-400">—</p>
                                    @endif
                                </dl>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
            <div class="mt-4">{{ $audits->links() }}</div>
        @endif
    </x-page-section>
</div>
