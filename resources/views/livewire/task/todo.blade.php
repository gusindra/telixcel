@php
    $statusMeta = [
        'progress'  => ['dot' => 'bg-blue-500',    'soft' => 'bg-blue-50 text-blue-600'],
        'pending'   => ['dot' => 'bg-amber-500',   'soft' => 'bg-amber-50 text-amber-600'],
        'complete'  => ['dot' => 'bg-emerald-500', 'soft' => 'bg-emerald-50 text-emerald-600'],
        'declined'  => ['dot' => 'bg-red-500',     'soft' => 'bg-red-50 text-red-600'],
        'cancelled' => ['dot' => 'bg-gray-500',    'soft' => 'bg-gray-100 text-gray-600'],
        'aborted'   => ['dot' => 'bg-yellow-500',  'soft' => 'bg-yellow-50 text-yellow-600'],
    ];
    $closedStatuses = ['complete', 'declined', 'cancelled', 'aborted'];
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

    {{-- Task list: one bordered container, root rows separated by dividers --}}
    <div class="border border-gray-100 dark:border-slate-600 rounded-lg divide-y divide-gray-100 dark:divide-slate-600">
        @forelse ($roots as $root)
            @include('livewire.task.task-node', ['task' => $root, 'depth' => 0, 'isFirst' => $loop->first, 'isLast' => $loop->last])
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
                <span class="block mt-1 text-sm text-amber-600">{{ __('Its sub-tasks will be moved up one level and kept.') }}</span>
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingDelete', false)">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-danger-button class="ml-2" wire:click="deleteTask">{{ __('Delete') }}</x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>

    {{-- Mandatory comment modal for terminal statuses (declined / cancelled / aborted) --}}
    @php
        $statusIcon = [
            'declined'  => 'M6 18L18 6M6 6l12 12',                                            // X
            'cancelled' => 'M18.364 5.636L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z',     // ban
            'aborted'   => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.5 9.5h5v5h-5z',               // stop
        ];
        $cStatus = $commentStatus ?: 'declined';
        $cMeta   = $statusMeta[$cStatus] ?? ['dot' => 'bg-gray-400', 'soft' => 'bg-gray-100 text-gray-600'];
        $cIcon   = $statusIcon[$cStatus] ?? 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z';
    @endphp
    <x-jet-dialog-modal wire:model="commentModal">
        <x-slot name="title">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center h-10 w-10 rounded-full flex-shrink-0 {{ $cMeta['soft'] }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cIcon }}" />
                    </svg>
                </span>
                <div>
                    <div class="text-base font-semibold text-gray-900 dark:text-slate-100 leading-tight">
                        {{ __('Set task as') }} <span class="capitalize">{{ $cStatus }}</span>
                    </div>
                    <div class="text-xs font-normal text-gray-400">{{ __('A reason is required for this status') }}</div>
                </div>
            </div>
        </x-slot>
        <x-slot name="content">
            <div x-data="{ note: @entangle('statusComment').defer }">
                <label class="block text-sm font-medium text-gray-600 dark:text-slate-300 mb-1.5">
                    {{ __('Reason / comment') }} <span class="text-red-500">*</span>
                </label>
                <textarea x-model="note" rows="4" maxlength="500"
                    class="border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-lg shadow-sm block w-full text-sm resize-none"
                    placeholder="{{ __('e.g. Client postponed the project / duplicate task / out of scope...') }}"></textarea>
                <div class="flex items-center justify-between mt-1.5">
                    <x-jet-input-error for="statusComment" />
                    <span class="text-[11px] text-gray-400 ml-auto" x-text="(note ? note.length : 0) + ' / 500'"></span>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('commentModal', false)">{{ __('Cancel') }}</x-jet-secondary-button>
            <button wire:click="confirmStatusComment" wire:loading.attr="disabled"
                    class="ml-2 inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs text-white uppercase tracking-widest transition focus:outline-none disabled:opacity-50 {{ $cMeta['dot'] }} hover:opacity-90">
                {{ __('Confirm') }}
            </button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
