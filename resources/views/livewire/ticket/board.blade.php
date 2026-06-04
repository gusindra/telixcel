@php
    $statusMeta = [
        'open'        => ['label' => 'Open',        'dot' => 'bg-blue-500',    'soft' => 'bg-blue-50 text-blue-600'],
        'in_progress' => ['label' => 'In Progress', 'dot' => 'bg-amber-500',   'soft' => 'bg-amber-50 text-amber-600'],
        'resolved'    => ['label' => 'Resolved',    'dot' => 'bg-indigo-500',  'soft' => 'bg-indigo-50 text-indigo-600'],
        'closed'      => ['label' => 'Closed',      'dot' => 'bg-emerald-500', 'soft' => 'bg-emerald-50 text-emerald-600'],
    ];
    $sMeta = fn ($s) => $statusMeta[$s] ?? ['label' => ucfirst($s), 'dot' => 'bg-gray-400', 'soft' => 'bg-gray-100 text-gray-600'];
    $prioBadge = [
        'low' => 'bg-gray-100 text-gray-500', 'medium' => 'bg-blue-50 text-blue-600', 'high' => 'bg-red-50 text-red-600',
    ];
    $taskStatus = [
        'progress' => 'text-blue-600', 'pending' => 'text-amber-600', 'complete' => 'text-emerald-600 line-through',
    ];
@endphp

<div class="py-2">
    {{-- Create ticket --}}
    <div class="flex flex-col sm:flex-row sm:items-end gap-3 mb-6">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('New Ticket') }}</label>
            <input type="text" wire:model.defer="reasons" wire:keydown.enter="createTicket"
                   placeholder="{{ __('Describe the request / issue...') }}"
                   class="border-gray-300 dark:bg-slate-800 dark:text-slate-200 rounded-md shadow-sm mt-1 block w-full text-sm" />
            @error('reasons') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Priority') }}</label>
            <select wire:model="priority" class="border-gray-300 dark:bg-slate-800 dark:text-slate-200 rounded-md shadow-sm mt-1 block w-full text-sm">
                @foreach ($priorities as $p)<option value="{{ $p }}">{{ ucfirst($p) }}</option>@endforeach
            </select>
        </div>
        <button wire:click="createTicket"
                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            {{ __('+ Add Ticket') }}
        </button>
    </div>

    <x-jet-action-message class="mb-3" on="saved">{{ __('Saved.') }}</x-jet-action-message>

    {{-- Ticket list (minimalist: one container, divided rows) --}}
    <div class="border border-gray-100 dark:border-slate-600 rounded-lg divide-y divide-gray-100 dark:divide-slate-600 overflow-hidden">
        @forelse ($tickets as $ticket)
            @php
                $m = $sMeta($ticket->status);
                $todoCount = $ticket->tasks->count();
                $allDone = $todoCount && $ticket->tasks->every(fn ($t) => $t->status === 'complete');
                $readyToClose = $allDone && $ticket->status !== 'closed';
            @endphp
            <div x-data="{ open: false }" class="bg-white dark:bg-slate-700">
                <div class="flex items-center gap-3 px-4 py-3">
                    <span class="h-2 w-2 rounded-full flex-shrink-0 {{ $m['dot'] }}"></span>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs text-gray-400">#{{ $ticket->id }}</span>
                            <span class="font-semibold text-sm text-gray-900 dark:text-slate-100 truncate">{{ $ticket->reasons }}</span>
                            <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded {{ $prioBadge[$ticket->priority] ?? 'bg-gray-100 text-gray-500' }}">{{ $ticket->priority }}</span>
                            @if($readyToClose)
                                <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600">{{ __('Ready to close') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-1 text-[11px] text-gray-400 flex-wrap">
                            <span>{{ optional($ticket->createdBy)->name ?: ($ticket->created_by ?: '-') }}</span>
                            <span>{{ optional($ticket->created_at)->format('d M Y') }}</span>
                            @if($todoCount)
                                <span class="inline-flex items-center gap-1 text-violet-600">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                                    {{ $ticket->tasks->where('status','complete')->count() }}/{{ $todoCount }} {{ __('to-do') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- assign to role --}}
                        <select wire:change="assign({{ $ticket->id }}, $event.target.value)"
                                class="text-[11px] py-1 pl-2 pr-6 rounded-md border border-gray-200 dark:bg-slate-700 dark:text-slate-200 cursor-pointer focus:outline-none focus:ring-1 focus:ring-gray-300">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach ($roles as $r)<option value="{{ $r->id }}" @selected($ticket->role_id == $r->id)>{{ $r->name }}</option>@endforeach
                        </select>

                        {{-- status --}}
                        <select wire:change="setStatus({{ $ticket->id }}, $event.target.value)"
                                class="text-[11px] font-medium py-1 pl-2 pr-6 rounded-md border-0 {{ $m['soft'] }} cursor-pointer focus:outline-none focus:ring-1 focus:ring-gray-300">
                            <option value="open" @selected($ticket->status==='open')>Open</option>
                            <option value="in_progress" @selected($ticket->status==='in_progress')>In Progress</option>
                            <option value="resolved" @selected($ticket->status==='resolved')>Resolved</option>
                            <option value="closed" @selected($ticket->status==='closed')>Closed</option>
                        </select>

                        <button wire:click="openTodo({{ $ticket->id }})"
                                class="text-xs font-medium text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-300 rounded-md px-3 py-1.5 transition">{{ __('+ To-do') }}</button>
                        <button wire:click="openLink({{ $ticket->id }})"
                                class="text-xs font-medium text-violet-600 hover:text-white hover:bg-violet-600 border border-violet-300 rounded-md px-3 py-1.5 transition">{{ __('Link To-do') }}</button>
                        <button wire:click="confirmDelete({{ $ticket->id }})"
                                class="text-xs font-medium text-red-600 hover:text-white hover:bg-red-600 border border-red-300 rounded-md px-2 py-1.5 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        @if($todoCount)
                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                                <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        @else
                            <span class="w-6"></span>
                        @endif
                    </div>
                </div>

                {{-- linked to-dos --}}
                @if($todoCount)
                    <div x-show="open" x-cloak class="border-t border-gray-100 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-800/30">
                        @foreach($ticket->tasks as $task)
                            <div class="flex items-center gap-3 pl-10 pr-4 py-2 border-b border-gray-100 dark:border-slate-600 last:border-0">
                                <span class="flex-1 text-sm truncate {{ $taskStatus[$task->status] ?? 'text-gray-700 dark:text-slate-300' }}">{{ $task->title }}</span>
                                <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-violet-50 text-violet-600">{{ $task->type }}</span>
                                <span class="text-[11px] {{ $taskStatus[$task->status] ?? 'text-gray-500' }}">{{ ucfirst($task->status) }}</span>
                                <button wire:click="unlinkTask({{ $task->id }})" title="{{ __('Unlink') }}" class="text-gray-300 hover:text-red-600 focus:outline-none">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5m-1.328-5.156a4 4 0 015.656 0l3-3a4 4 0 10-5.656-5.656l-1.5 1.5"/></svg>
                                </button>
                            </div>
                        @endforeach
                        <div class="pl-10 pr-4 py-2 text-[11px] text-gray-400">{{ __('Manage these tasks in the To-do List / Dashboard.') }}</div>
                    </div>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-14 text-center bg-white dark:bg-slate-700">
                <p class="text-sm text-gray-400">{{ __('No tickets yet.') }}</p>
            </div>
        @endforelse
    </div>

    @if($tickets->hasPages())
        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    @endif

    {{-- Create To-do modal --}}
    <x-jet-dialog-modal wire:model="showTodoModal">
        <x-slot name="title">{{ __('Create To-do from Ticket') }} #{{ $todoTicketId }}</x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2">
                    <x-jet-label value="{{ __('Task') }}" />
                    <x-jet-input type="text" class="mt-1 block w-full" wire:model.defer="todoTitle" />
                    <x-jet-input-error for="todoTitle" class="mt-2" />
                </div>
                <div>
                    <x-jet-label value="{{ __('Type') }}" />
                    <select wire:model="todoType" class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 rounded-md shadow-sm mt-1 block w-full text-sm">
                        <option value="">-- {{ __('Select Type') }} --</option>
                        @foreach ($types as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach
                    </select>
                    <x-jet-input-error for="todoType" class="mt-2" />
                </div>
                <div>
                    <x-jet-label value="{{ __('Target Date') }}" />
                    <x-jet-input type="date" class="mt-1 block w-full" wire:model.defer="todoTarget" />
                    <x-jet-input-error for="todoTarget" class="mt-2" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('showTodoModal')">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="createTodo" wire:loading.attr="disabled">{{ __('Create To-do') }}</x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    {{-- Link existing To-do modal --}}
    <x-jet-dialog-modal wire:model="showLinkModal">
        <x-slot name="title">{{ __('Link existing To-do to Ticket') }} #{{ $linkTicketId }}</x-slot>
        <x-slot name="content">
            <x-jet-label value="{{ __('Choose a to-do') }}" />
            <select wire:model="linkTaskId" class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 rounded-md shadow-sm mt-1 block w-full text-sm">
                <option value="">-- {{ __('Select to-do') }} --</option>
                @forelse ($availableTasks as $t)
                    <option value="{{ $t->id }}">{{ $t->title }} ({{ ucfirst($t->type) }})</option>
                @empty
                    <option value="" disabled>{{ __('No unlinked to-do available') }}</option>
                @endforelse
            </select>
            <x-jet-input-error for="linkTaskId" class="mt-2" />
            <p class="mt-2 text-xs text-gray-400">{{ __('Only root to-dos not yet linked to a ticket are shown.') }}</p>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('showLinkModal')">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="linkTask" wire:loading.attr="disabled">{{ __('Link') }}</x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    {{-- Delete confirmation --}}
    <x-jet-confirmation-modal wire:model="confirmingDelete">
        <x-slot name="title">{{ __('Delete Ticket') }}</x-slot>
        <x-slot name="content">{{ __('Delete this ticket? Linked to-dos are kept but unlinked.') }}</x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingDelete', false)">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-danger-button class="ml-2" wire:click="deleteTicket">{{ __('Delete') }}</x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
