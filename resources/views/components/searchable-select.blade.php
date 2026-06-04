@props(['field', 'options', 'placeholder' => '-- Select --', 'sub' => null])

{{-- Searchable single-select bound to a Livewire property ($field), defer-synced.
     Usage: <x-searchable-select field="selectedUser" :options="$users" sub="email" /> --}}
<div x-data="{ open: false, q: '', sel: '', labels: {{ \Illuminate\Support\Js::from($options->mapWithKeys(fn ($o) => [(string) $o->id => $o->name])) }} }"
     x-init="sel = $refs.real.value || ''; q = (sel && labels[sel]) ? labels[sel] : ''"
     @click.away="open = false" class="relative flex-1">
    <input type="text" x-model="q" @focus="open = true" @click="open = true"
           @input="sel = ''; $refs.real.value = ''; $refs.real.dispatchEvent(new Event('input')); open = true"
           placeholder="{{ $placeholder }}"
           class="w-full h-10 border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm text-sm" />

    {{-- real value sent to Livewire on next action --}}
    <input type="hidden" x-ref="real" wire:model.defer="{{ $field }}">

    <div x-show="open" style="display:none;"
         class="absolute z-30 mt-1 w-full max-h-56 overflow-y-auto bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-md shadow-lg">
        @forelse ($options as $opt)
            @php $s = $sub ? ($opt->{$sub} ?? '') : ''; @endphp
            <div x-show="q === '' || {{ \Illuminate\Support\Js::from(strtolower($opt->name . ' ' . $s)) }}.includes(q.toLowerCase())"
                 @mousedown.prevent="q = {{ \Illuminate\Support\Js::from($opt->name) }}; sel = '{{ $opt->id }}'; $refs.real.value = '{{ $opt->id }}'; $refs.real.dispatchEvent(new Event('input')); open = false"
                 :class="sel === '{{ $opt->id }}' ? 'bg-blue-50 dark:bg-slate-700 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-slate-200'"
                 class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 flex items-center justify-between gap-2">
                <span class="truncate">{{ $opt->name }}@if($s)<span class="text-gray-400 text-xs"> ({{ $s }})</span>@endif</span>
                <svg x-show="sel === '{{ $opt->id }}'" style="display:none;" class="h-4 w-4 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
        @empty
            <div class="px-3 py-2 text-sm text-gray-400">{{ __('No options') }}</div>
        @endforelse
    </div>
</div>
