<div class="inline-flex items-center gap-2 whitespace-nowrap">
    <a href="{{ route('ai.applications.show', $uuid) }}" class="tx-row-link">{{ __('Detail') }}</a>
    <button type="button" class="tx-row-link" wire:click="$emit('aiAppToggle', {{ (int) $id }})">{{ !empty($active) ? 'Disable' : 'Enable' }}</button>
</div>
