<div>
    @if($viewUrl)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500/75" wire:click="close"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full rounded-lg bg-white dark:bg-slate-700 shadow-xl" style="max-width:96vw; width:96vw;">

                    {{-- header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-slate-600">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-slate-200">{{ $viewTitle }}</h3>
                        <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- iframe (navbar hidden via embed=1) --}}
                    <div class="px-6 py-5">
                        <iframe src="{{ $viewUrl }}" style="height:82vh;" class="w-full rounded-md border border-gray-200 dark:border-slate-600 bg-white"
                                title="{{ $viewTitle }}"></iframe>
                    </div>

                    {{-- footer --}}
                    <div class="flex justify-end gap-2 px-6 py-4 bg-gray-50 dark:bg-slate-800 rounded-b-lg">
                        <a href="{{ $viewUrl }}" target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">{{ __('Open in new tab') }}</a>
                        <button type="button" wire:click="close"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
