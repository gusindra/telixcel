{{--
    Recursive task row partial.
    Variables inherited from parent scope: $meta, $typeBadge, $prioBadge, $dashboard
    Variables passed per include: $task (Task model), $depth (int, 0 = root)
--}}
@php
    $depth   = $depth ?? 0;
    $isRoot  = ($depth === 0);
    $isFirst = $isFirst ?? false;
    $isLast  = $isLast  ?? false;
    $cTotal   = $task->childNodes->count();
    $cDone    = $task->childNodes->where('status', 'complete')->count();
    $isClosed = in_array($task->status, ['complete', 'declined', 'cancelled', 'aborted']);
    $isReason = in_array($task->status, ['declined', 'cancelled', 'aborted']);
    $m        = $meta($task->status);
    $indent   = 16 + $depth * 28;   // px left padding for sub-levels
@endphp

<div x-data="{ open: false }"
     class="bg-white dark:bg-slate-700
            {{ !$isRoot ? 'border-t border-gray-100 dark:border-slate-600' : '' }}
            {{ $isRoot && $isFirst ? 'rounded-t-lg' : '' }}
            {{ $isRoot && $isLast && !$cTotal ? 'rounded-b-lg' : '' }}
            {{ $isRoot && $task->priority === 'high' && !$isClosed ? 'border-l-2 border-red-400' : '' }}">

    {{-- ROW --}}
    <div class="flex items-center gap-3 pr-4 {{ $isRoot ? 'px-4 py-3' : 'py-2' }}"
         style="{{ !$isRoot ? 'padding-left:' . $indent . 'px' : '' }}">

        <span class="{{ $isRoot ? 'h-2 w-2' : 'h-1.5 w-1.5' }} rounded-full flex-shrink-0 {{ $m['dot'] }}"></span>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="{{ $isRoot ? 'text-sm' : 'text-xs' }} truncate
                    {{ $isClosed ? 'line-through text-gray-400' : ($isRoot ? 'font-medium text-gray-800 dark:text-slate-100' : 'text-gray-700 dark:text-slate-300') }}">{{ $task->title }}</span>
                @if($isRoot && $task->type)
                    <span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded {{ $typeBadge($task->type) }}">{{ $task->type }}</span>
                @endif
                @if($task->priority)
                    <span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded {{ $prioBadge($task->priority) }}">{{ $task->priority }}</span>
                @endif
                @if($task->ticket_id)
                    <span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded bg-rose-50 text-rose-600">Ticket #{{ $task->ticket_id }}</span>
                @endif
            </div>
            <div class="flex items-center gap-3 mt-0.5 text-[11px] text-gray-400">
                @if($task->target_date)<span>{{ $task->target_date->format('d M Y') }}</span>@endif
                @if(($dashboard ?? false) && $isRoot && $task->owner)<span>{{ $task->owner->name }}</span>@endif
                @if($cTotal)<span>{{ $cDone }}/{{ $cTotal }} {{ __('sub') }}</span>@endif
            </div>
            @if($isReason && $task->status_note)
                <div class="mt-1 inline-flex items-start gap-1.5 text-[11px] {{ $m['soft'] }} rounded-md px-2 py-1 max-w-full">
                    <svg class="h-3 w-3 mt-0.5 flex-shrink-0 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7.17 4A3.17 3.17 0 004 7.17v1.66A3.17 3.17 0 007.17 12H8v.5A2.5 2.5 0 015.5 15a.5.5 0 000 1A3.5 3.5 0 009 12.5V7.17A3.17 3.17 0 005.83 4h1.34zm8 0A3.17 3.17 0 0012 7.17v1.66A3.17 3.17 0 0015.17 12H16v.5a2.5 2.5 0 01-2.5 2.5.5.5 0 000 1A3.5 3.5 0 0017 12.5V7.17A3.17 3.17 0 0013.83 4h1.34z"/>
                    </svg>
                    <span><span class="font-semibold capitalize">{{ $task->status }}:</span> {{ $task->status_note }}</span>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <select wire:key="status-{{ $task->id }}-{{ $task->status }}"
                    x-data="{ prev: '{{ $task->status }}' }"
                    @change="
                        const v = $event.target.value;
                        if (['declined','cancelled','aborted'].includes(v)) {
                            $wire.requestStatusComment({{ $task->id }}, v);
                            $event.target.value = prev;   /* revert; modal drives the real change */
                        } else {
                            prev = v;
                            $wire.setStatus({{ $task->id }}, v);
                        }
                    "
                    class="text-[11px] font-medium py-1 pl-2 pr-6 rounded-md border-0 {{ $m['soft'] }} cursor-pointer focus:outline-none focus:ring-1 focus:ring-gray-300">
                <option value="progress"  @selected($task->status==='progress')>In Progress</option>
                <option value="pending"   @selected($task->status==='pending')>Pending</option>
                <option value="complete"  @selected($task->status==='complete')>Completed</option>
                <option value="declined"  @selected($task->status==='declined')>Declined</option>
                <option value="cancelled" @selected($task->status==='cancelled')>Cancelled</option>
                <option value="aborted"   @selected($task->status==='aborted')>Aborted</option>
            </select>

            @unless($dashboard ?? false)
            <div x-data="{ m:false }" class="relative">
                <button @click="m=!m" class="text-gray-300 hover:text-gray-500 focus:outline-none p-1">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 2a2 2 0 110 4 2 2 0 010-4zm0 6a2 2 0 110 4 2 2 0 010-4z"/>
                    </svg>
                </button>
                <div x-show="m" @click.away="m=false" x-cloak
                     class="absolute right-0 mt-1 w-40 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-gray-100 dark:border-slate-600 py-1 z-50">
                    <button wire:click="actionShowModal({{ $task->id }})" @click="m=false"
                            class="block w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                        {{ __('Add sub-task') }}
                    </button>
                    <button wire:click="confirmDelete({{ $task->id }})" @click="m=false"
                            class="block w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
            @endunless

            @if($cTotal)
                <button @click="open = !open" class="text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            @else
                <span class="w-6"></span>
            @endif
        </div>
    </div>

    {{-- CHILDREN — recursive include with depth+1 --}}
    @if($cTotal)
        <div x-show="open" x-cloak class="bg-gray-50/60 dark:bg-slate-800/30">
            @foreach($task->childNodes as $child)
                @include('livewire.task.task-node', ['task' => $child, 'depth' => $depth + 1])
            @endforeach
            @unless($dashboard ?? false)
                <div class="py-2" style="padding-left: {{ $indent + 28 }}px">
                    <button wire:click="actionShowModal({{ $task->id }})"
                            class="text-xs font-medium text-blue-600 hover:text-blue-800 focus:outline-none">
                        + {{ __('Add sub-task') }}
                    </button>
                </div>
            @endunless
        </div>
    @endif
</div>
