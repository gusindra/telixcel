<div class="tx-stack">
    @if(session('ai_saved'))
        <p class="tx-13 m-0" style="color:#047857;">{{ __('Settings saved.') }}</p>
    @endif

    <x-page-section :title="$application->name">
        <x-slot name="toolbar">
            <button type="button" class="tx-btn tx-btn-ghost" wire:click="toggleStatus">
                {{ $application->isActive() ? __('Disable') : __('Enable') }}
            </button>
            <x-jet-confirms-password wire:then="regenerateKey">
                <button type="button" class="tx-btn tx-btn-ghost">
                    {{ __('Regenerate key') }}
                </button>
            </x-jet-confirms-password>
        </x-slot>

        <h4 class="tx-section-title">{{ __('Client connection') }}</h4>
        <dl class="tx-kv" x-data="{ copied: '' }">
            <div class="tx-kv-row">
                <dt class="tx-kv-label">{{ __('Endpoint') }}</dt>
                <dd class="tx-kv-value">{{ $this->clientEndpoint }}</dd>
                <div class="tx-kv-actions">
                    <button type="button" class="tx-btn tx-btn-ghost" @click="navigator.clipboard.writeText({{ json_encode($this->clientEndpoint) }}).then(() => { copied = 'endpoint'; setTimeout(() => copied = '', 1400) })">
                        <span x-text="copied === 'endpoint' ? '{{ __('Copied') }}' : '{{ __('Copy') }}'">{{ __('Copy') }}</span>
                    </button>
                </div>
            </div>
            <div class="tx-kv-row">
                <dt class="tx-kv-label">{{ __('Completions') }}</dt>
                <dd class="tx-kv-value">{{ $this->completionsUrl }}</dd>
                <div class="tx-kv-actions">
                    <button type="button" class="tx-btn tx-btn-ghost" @click="navigator.clipboard.writeText({{ json_encode($this->completionsUrl) }}).then(() => { copied = 'completions'; setTimeout(() => copied = '', 1400) })">
                        <span x-text="copied === 'completions' ? '{{ __('Copied') }}' : '{{ __('Copy') }}'">{{ __('Copy') }}</span>
                    </button>
                </div>
            </div>
            <div class="tx-kv-row">
                <dt class="tx-kv-label">{{ __('API key') }}</dt>
                @if($plainApiKey)
                    <dd class="tx-kv-value">{{ $plainApiKey }}</dd>
                    <div class="tx-kv-actions">
                        <button type="button" class="tx-btn tx-btn-ghost" @click="navigator.clipboard.writeText({{ json_encode($plainApiKey) }}).then(() => { copied = 'key'; setTimeout(() => copied = '', 1400) })">
                            <span x-text="copied === 'key' ? '{{ __('Copied') }}' : '{{ __('Copy') }}'">{{ __('Copy') }}</span>
                        </button>
                        <button type="button" class="tx-btn tx-btn-ghost" wire:click="dismissKey">{{ __('Hide') }}</button>
                    </div>
                @else
                    <dd class="tx-kv-value">{{ $application->api_key_prefix ? $application->api_key_prefix.'…' : '—' }}</dd>
                    <div class="tx-kv-actions">
                        <x-jet-confirms-password wire:then="revealKey">
                            <button type="button" class="tx-btn tx-btn-ghost">{{ __('Show key') }}</button>
                        </x-jet-confirms-password>
                    </div>
                @endif
            </div>
        </dl>
        @if($keyRevealError)
            <p class="text-sm mt-2 mb-0" style="color:#b45309;">{{ $keyRevealError }}</p>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
            <div>
                <x-jet-label value="Name" />
                <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="input.name" />
                <x-jet-input-error for="input.name" class="mt-2" />
            </div>
            <div>
                <x-jet-label value="Slug" />
                <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="input.slug" />
                <x-jet-input-error for="input.slug" class="mt-2" />
            </div>
            <div>
                <x-jet-label value="Status" />
                <x-select class="mt-1" wire:model.defer="input.status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </x-select>
            </div>
            <div>
                <x-jet-label value="Rate limit / minute" />
                <x-jet-input type="number" class="mt-1 block w-full" wire:model.defer="input.rate_limit_per_minute" />
            </div>
            <div>
                <x-jet-label value="Monthly cost limit" />
                <div class="mt-1 flex gap-2">
                    <x-select class="w-28" wire:model.defer="input.cost_currency">
                        <option value="USD">USD</option>
                        <option value="IDR">IDR</option>
                    </x-select>
                    <x-jet-input type="number" step="0.01" class="block w-full" wire:model.defer="input.monthly_cost_limit" />
                </div>
                <p class="text-xs tx-fg-muted mt-1 mb-0">{{ __('Empty means no monthly cap. IDR uses AI_USD_IDR from .env.') }}</p>
            </div>
        </div>

        <div class="mt-4">
            <label class="inline-flex items-center">
                <input type="checkbox" class="rounded" wire:model.defer="input.require_end_user">
                <span class="ml-2 text-sm">{{ __('Require end user (e.g. Dika in Hireach)') }}</span>
            </label>
        </div>

        <div class="mt-6">
            <div class="flex items-center justify-between gap-2">
                <x-jet-label value="Allowed models" />
                <button type="button" class="tx-btn tx-btn-ghost" wire:click="recheckModels" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="recheckModels">{{ __('Refresh from upstream') }}</span>
                    <span wire:loading wire:target="recheckModels">{{ __('Checking…') }}</span>
                </button>
            </div>
            <p class="text-sm tx-fg-muted mt-1 mb-2">
                {{ $modelsStatus ?: __('Model yang dapat digunakan') }}
            </p>
            @if($modelsError)
                <p class="text-sm mb-2" style="color:#b45309;">{{ $modelsError }}</p>
            @endif
            @if($catalog->isEmpty())
                <p class="text-sm tx-fg-muted m-0">{{ __('No models yet. Refresh to load the list from upstream.') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-1">
                    @foreach($catalog as $model)
                        <label class="inline-flex items-start">
                            <input type="checkbox" class="rounded mt-0.5" value="{{ $model->id }}" wire:model.defer="selectedModels">
                            <span class="ml-2 text-sm" title="{{ $model->model_identifier }}">
                                <span class="tx-fg">{{ $model->publicName() }}</span>
                                <span class="tx-fg-muted"> · {{ $model->sourceLabel() }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-6">
            <x-jet-button type="button" wire:click="save">{{ __('Save') }}</x-jet-button>
        </div>
    </x-page-section>
</div>
