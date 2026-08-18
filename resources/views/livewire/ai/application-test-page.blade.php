<div>
    <x-page-section :title="__('Test')">
        <p class="text-sm tx-fg-muted mt-0 mb-4">
            {{ __('Sends one chat completion with the server key. It is not logged as application usage and does not count toward quota or cost.') }}
        </p>
        @if(! $keyConfigured)
            <p class="text-sm mb-3" style="color:#b45309;">{{ __('Set AI_BASE_URL and AI_API_KEY first.') }}</p>
        @endif
        @if($modelsError)
            <p class="text-sm mb-3" style="color:#b45309;">{{ $modelsError }}</p>
        @endif
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between gap-2">
                    <x-jet-label value="Model" />
                    <button type="button" class="tx-btn tx-btn-ghost" wire:click="refreshModels" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="refreshModels">{{ __('Refresh models') }}</span>
                        <span wire:loading wire:target="refreshModels">{{ __('Checking…') }}</span>
                    </button>
                </div>
                @if($testModels->isNotEmpty())
                    <x-select class="mt-1" wire:model.defer="testModel" :disabled="! $keyConfigured">
                        @foreach($testModels as $model)
                            <option value="{{ $model->model_identifier }}">{{ $model->publicName() }} · {{ $model->sourceLabel() }}</option>
                        @endforeach
                    </x-select>
                @else
                    <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="testModel" placeholder="kr/auto" :disabled="! $keyConfigured" />
                @endif
                <x-jet-input-error for="testModel" class="mt-2" />
            </div>
            <div class="sm:col-span-2">
                <x-jet-label value="Prompt" />
                <textarea class="mt-1 block w-full border-gray-300 rounded-md dark:bg-slate-800" rows="4" wire:model.defer="testPrompt"></textarea>
                <x-jet-input-error for="testPrompt" class="mt-2" />
            </div>
        </div>
        <button type="button" class="tx-btn mt-3" wire:click="ping" wire:loading.attr="disabled" @if(! $keyConfigured) disabled @endif>
            <span wire:loading.remove wire:target="ping">{{ __('Send test') }}</span>
            <span wire:loading wire:target="ping">{{ __('Sending…') }}</span>
        </button>
        @if($testError)
            <div class="text-sm mt-3" style="color:#be123c;">{{ $testError }}</div>
        @endif
        @if($testContent)
            <div class="mt-3">
                <p class="tx-11 tx-fg-muted m-0 mb-1">{{ __('Response') }}</p>
                <pre class="text-sm font-mono whitespace-pre-wrap break-words m-0 p-3 rounded-lg" style="background: var(--tx-surface-1);">{{ $testContent }}</pre>
                @if(! empty($testMeta['model']))
                    <p class="tx-11 tx-fg-muted mt-2 mb-0">
                        {{ __('Upstream model') }}: <span class="font-mono">{{ $testMeta['model'] }}</span>
                    </p>
                @endif
                @if(! empty($testMeta['usage']))
                    <p class="tx-11 tx-fg-muted mt-1 mb-0">
                        {{ __('Tokens') }}:
                        {{ (int) data_get($testMeta['usage'], 'prompt_tokens', 0) }} in
                        · {{ (int) data_get($testMeta['usage'], 'completion_tokens', 0) }} out
                        · {{ (int) data_get($testMeta['usage'], 'total_tokens', 0) }} total
                    </p>
                @endif
            </div>
        @endif
    </x-page-section>
</div>
