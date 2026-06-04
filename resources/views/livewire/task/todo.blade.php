@php
    $statusMeta = [
        'progress' => ['dot' => 'bg-blue-500',    'soft' => 'bg-blue-50 text-blue-600'],
        'pending'  => ['dot' => 'bg-amber-500',   'soft' => 'bg-amber-50 text-amber-600'],
        'complete' => ['dot' => 'bg-emerald-500', 'soft' => 'bg-emerald-50 text-emerald-600'],
    ];
    $meta = fn ($s) => $statusMeta[$s] ?? ['dot' => 'bg-gray-400', 'soft' => 'bg-gray-100 text-gray-600'];
    $typeBadge = fn ($t) => [
        'finance' => 'bg-indigo-50 text-indigo-600', 'admin' => 'bg-sky-50 text-sky-600', 'operasional' => 'bg-violet-50 text-violet-600',
    ][$t] ?? 'bg-gray-100 text-gray-500';
    $prioBadge = fn ($p) => [
        'low' => 'bg-gray-100 text-gray-500', 'medium' => 'bg-blue-50 text-blue-600', 'high' => 'bg-red-50 text-red-600',
    ][$p] ?? 'bg-gray-100 text-gray-500';
@endphp

<div>
    {{-- Header (hidden on dashboard) --}}
    @unless($dashboard ?? false)
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100">{{ __('To-do List') }}</h3>
            <span class="text-xs text-gray-400">{{ $roots->total() }} {{ __('tasks') }}</span>
        </div>
        <button wire:click="actionShowModal(0)"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition focus:outline-none">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add Task') }}
        </button>
    </div>
    @endunless

    <x-jet-action-message class="mb-3" on="task_saved">{{ __('Task saved.') }}</x-jet-action-message>

    {{-- Minimalist list: one bordered container, rows separated by dividers --}}
    <div class="border border-gray-100 dark:border-slate-600 rounded-lg divide-y divide-gray-100 dark:divide-slate-600 overflow-hidden">
        @forelse ($roots as $root)
            @php
                $cTotal = $root->childNodes->count();
                $cDone  = $root->childNodes->where('status', 'complete')->count();
                $isDone = $root->status === 'complete';
                $m = $meta($root->status);
            @endphp
            <div x-data="{ open: false }" class="bg-white dark:bg-slate-700 {{ $root->priority === 'high' && ! $isDone ? 'border-l-2 border-red-400' : '' }}">
                {{-- ROW --}}
                <div class="flex items-center gap-3 px-4 py-3">
                    <span class="h-2 w-2 rounded-full flex-shrink-0 {{ $m['dot'] }}"></span>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm truncate {{ $isDone ? 'line-through text-gray-400' : 'font-medium text-gray-800 dark:text-slate-100' }}">{{ $root->title }}</span>
                            @if($root->type)<span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded {{ $typeBadge($root->type) }}">{{ $root->type }}</span>@endif
                            @if($root->priority)<span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded {{ $prioBadge($root->priority) }}">{{ $root->priority }}</span>@endif
                            @if($root->ticket_id)<span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded bg-rose-50 text-rose-600">Ticket #{{ $root->ticket_id }}</span>@endif
                        </div>
                        <div class="flex items-center gap-3 mt-0.5 text-[11px] text-gray-400">
                            @if($root->target_date)<span>{{ $root->target_date->format('d M Y') }}</span>@endif
                            @if(($dashboard ?? false) && $root->owner)<span>{{ $root->owner->name }}</span>@endif
                            @if($cTotal)<span>{{ $cDone }}/{{ $cTotal }} {{ __('sub') }}</span>@endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <select wire:change="setStatus({{ $root->id }}, $event.target.value)"
                                class="text-[11px] font-medium py-1 pl-2 pr-6 rounded-md border-0 {{ $m['soft'] }} cursor-pointer focus:outline-none focus:ring-1 focus:ring-gray-300">
                            <option value="progress" @selected($root->status==='progress')>In Progress</option>
                            <option value="pending" @selected($root->status==='pending')>Pending</option>
                            <option value="complete" @selected($root->status==='complete')>Completed</option>
                        </select>

                        @unless($dashboard ?? false)
                        <div x-data="{ m:false }" class="relative">
                            <button @click="m=!m" class="text-gray-300 hover:text-gray-500 focus:outline-none p-1"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 2a2 2 0 110 4 2 2 0 010-4zm0 6a2 2 0 110 4 2 2 0 010-4z"/></svg></button>
                            <div x-show="m" @click.away="m=false" x-cloak class="absolute right-0 mt-1 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-100 dark:border-slate-600 py-1 z-20">
                                <button wire:click="actionShowModal({{ $root->id }})" @click="m=false" class="block w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-slate-300 hover:bg-gray-50">{{ __('Add sub-task') }}</button>
                                <button wire:click="confirmDelete({{ $root->id }})" @click="m=false" class="block w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                            </div>
                        </div>
                        @endunless

                        @if($cTotal)
                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                                <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        @else
                            <span class="w-6"></span>
                        @endif
                    </div>
                </div>

                {{-- SUB-TASKS --}}
                @if($cTotal)
                    <div x-show="open" x-cloak class="bg-gray-50/60 dark:bg-slate-800/30 border-t border-gray-100 dark:border-slate-600">
                        @foreach($root->childNodes as $child)
                            @php $cd = $child->status === 'complete'; $cm = $meta($child->status); @endphp
                            <div class="flex items-center gap-3 pl-10 pr-4 py-2 border-b border-gray-100 dark:border-slate-600 last:border-0">
                                <span class="h-1.5 w-1.5 rounded-full flex-shrink-0 {{ $cm['dot'] }}"></span>
                                <span class="flex-1 text-sm truncate {{ $cd ? 'line-through text-gray-400' : 'text-gray-700 dark:text-slate-300' }}">{{ $child->title }}</span>
                                <select wire:change="setStatus({{ $child->id }}, $event.target.value)"
                                        class="text-[11px] font-medium py-1 pl-2 pr-6 rounded-md border-0 {{ $cm['soft'] }} cursor-pointer focus:outline-none focus:ring-1 focus:ring-gray-300">
                                    <option value="progress" @selected($child->status==='progress')>In Progress</option>
                                    <option value="pending" @selected($child->status==='pending')>Pending</option>
                                    <option value="complete" @selected($child->status==='complete')>Completed</option>
                                </select>
                                @unless($dashboard ?? false)
                                <button wire:click="confirmDelete({{ $child->id }})" class="text-gray-300 hover:text-red-600 focus:outline-none">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                @endunless
                            </div>
                        @endforeach
                        @unless($dashboard ?? false)
                        <div class="pl-10 pr-4 py-2">
                            <button wire:click="actionShowModal({{ $root->id }})" class="text-xs font-medium text-blue-600 hover:text-blue-800 focus:outline-none">+ {{ __('Add sub-task') }}</button>
                        </div>
                        @endunless
                    </div>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-14 text-center bg-white dark:bg-slate-700">
                <p class="text-sm text-gray-400">{{ __('No tasks yet.') }}</p>
                @unless($dashboard ?? false)
                <button wire:click="actionShowModal(0)" class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none">+ {{ __('Add your first task') }}</button>
                @endunless
            </div>
        @endforelse
    </div>

    @if($roots->hasPages())
        <div class="mt-4">
            {{ $roots->links() }}
        </div>
    @endif

    {{-- Add task modal --}}
    <x-jet-dialog-modal wire:model="showForm">
        <x-slot name="title">{{ $parent_id ? __('New Sub-task') : __('New Task') }}</x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @if($parent_id)
                    <div class="md:col-span-2 flex items-center gap-2 text-sm bg-blue-50 dark:bg-blue-900/20 rounded-md px-3 py-2">
                        <span class="text-gray-500 dark:text-slate-400">{{ __('Sub-task of') }}:</span>
                        <span class="font-medium text-gray-700 dark:text-slate-200">{{ optional(\App\Models\Task::find($parent_id))->title }}</span>
                    </div>
                @endif
                <div class="md:col-span-2">
                    <x-jet-label value="{{ __('Task') }}" />
                    <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="title" placeholder="{{ __('What needs to be done?') }}" />
                    <x-jet-input-error for="title" class="mt-2" />
                </div>
                @unless($parent_id)
                    <div>
                        <x-jet-label value="{{ __('Type') }}" />
                        <select wire:model.defer="type" class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 rounded-md shadow-sm mt-1 block w-full text-sm">
                            <option value="">-- {{ __('Select Type') }} --</option>
                            @foreach ($types as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach
                        </select>
                        <x-jet-input-error for="type" class="mt-2" />
                    </div>
                @endunless
                <div>
                    <x-jet-label value="{{ __('Priority') }}" />
                    <select wire:model.defer="priority" class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 rounded-md shadow-sm mt-1 block w-full text-sm">
                        @foreach ($priorities as $p)<option value="{{ $p }}">{{ ucfirst($p) }}</option>@endforeach
                    </select>
                    <x-jet-input-error for="priority" class="mt-2" />
                </div>
                <div>
                    <x-jet-label value="{{ __('Target Date') }}" />
                    <x-jet-input type="date" class="mt-1 block w-full" wire:model.defer="target_date" />
                    <x-jet-input-error for="target_date" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-jet-label value="{{ __('Source (client request, manual)') }}" />
                    <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="source" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('showForm')">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="create" wire:loading.attr="disabled">{{ __('Save Task') }}</x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    {{-- Delete confirmation modal --}}
    <x-jet-confirmation-modal wire:model="confirmingDelete">
        <x-slot name="title">{{ __('Delete Task') }}</x-slot>
        <x-slot name="content">
            {{ __('Are you sure you want to delete') }} <span class="font-semibold">"{{ $deleteTitle }}"</span>?
            @if($deleteIsParent)
                <span class="block mt-1 text-sm text-amber-600">{{ __('Its sub-tasks will be kept and moved to the top level.') }}</span>
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingDelete', false)">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-danger-button class="ml-2" wire:click="deleteTask">{{ __('Delete') }}</x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
