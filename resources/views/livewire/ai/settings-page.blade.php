<div class="tx-stack">
    @if(session('ai_saved'))
        <p class="tx-13 m-0" style="color:#047857;">{{ __('Settings saved.') }}</p>
    @endif

    <x-page-section :title="__('Upstream endpoint')">
        <p class="text-sm tx-fg-muted mt-0 mb-4">
            {{ __('OpenAI-compatible upstream that Telixcel calls. For example: http://localhost:20128/v1') }}
        </p>

        <div class="grid grid-cols-1 gap-4">
            <div>
                <x-jet-label value="Base URL" />
                <x-jet-input
                    type="text"
                    class="mt-1 block w-full font-mono"
                    placeholder="http://localhost:20128/v1"
                    wire:model.defer="base_url"
                />
                <x-jet-input-error for="base_url" class="mt-2" />
                <p class="text-xs tx-fg-muted mt-1 mb-0">
                    {{ __('Empty = fallback ke AI_BASE_URL di .env.') }}
                </p>
            </div>
            <div>
                <x-jet-label value="API key" />
                <x-jet-input
                    type="password"
                    class="mt-1 block w-full font-mono"
                    placeholder="sk-… (opsional)"
                    wire:model.defer="api_key"
                />
                <x-jet-input-error for="api_key" class="mt-2" />
                <p class="text-xs tx-fg-muted mt-1 mb-0">
                    {{ __('Empty = fallback ke AI_API_KEY di .env.') }}
                </p>
            </div>
        </div>

        <div class="mt-6">
            <x-jet-button type="button" wire:click="save">{{ __('Save') }}</x-jet-button>
        </div>
    </x-page-section>

    <x-page-section :title="__('Current configuration')">
        <dl class="text-sm space-y-2 m-0">
            <div class="flex gap-2">
                <dt class="tx-fg-muted w-40 shrink-0">{{ __('Effective endpoint') }}</dt>
                <dd class="font-mono m-0">{{ $effectiveBaseUrl ?: '—' }}</dd>
            </div>
            <div class="flex gap-2">
                <dt class="tx-fg-muted w-40 shrink-0">{{ __('Source') }}</dt>
                <dd class="m-0">{{ $fromDb ? __('Settings page') : __('.env') }}</dd>
            </div>
        </dl>
    </x-page-section>
</div>
