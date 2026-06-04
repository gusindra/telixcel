<x-app-layout>

    <div x-data="{ tab: 'project', sub: 'product' }">{{-- single Alpine scope for all tabs + commercial sub-tabs --}}

    {{-- Header --}}
    <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700">
        <div class="w-full px-4 sm:px-6 lg:px-8 pt-5 pb-4">
            {{-- Breadcrumb --}}
            <nav class="flex items-center text-xs text-gray-400 dark:text-slate-400 mb-3">
                <a href="{{ route('project') }}" class="hover:text-gray-600 dark:hover:text-slate-200 transition">{{ __('Project') }}</a>
                <svg class="h-3.5 w-3.5 mx-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600 dark:text-slate-200 font-medium capitalize">{{ $project->name }}</span>
            </nav>

            {{-- Title + status --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 font-bold text-lg uppercase">
                        {{ Str::substr($project->name, 0, 1) }}
                    </span>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white capitalize leading-tight">
                            {{ $project->name }}
                        </h1>
                        <span class="text-xs text-gray-400 dark:text-slate-400 capitalize">{{ $project->type }}</span>
                    </div>
                </div>
                @php
                    $st = strtolower($project->status);
                    $styles = [
                        'draft'    => ['bg-gray-100 text-gray-600 ring-gray-300', 'bg-gray-400'],
                        'submit'   => ['bg-blue-50 text-blue-700 ring-blue-300', 'bg-blue-500'],
                        'approved' => ['bg-green-50 text-green-700 ring-green-300', 'bg-green-500'],
                        'done'     => ['bg-green-50 text-green-700 ring-green-300', 'bg-green-500'],
                        'decline'  => ['bg-red-50 text-red-700 ring-red-300', 'bg-red-500'],
                    ];
                    [$cls, $dot] = $styles[$st] ?? ['bg-gray-100 text-gray-600 ring-gray-300', 'bg-gray-400'];
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide ring-1 ring-inset {{ $cls }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
                    {{ $project->status }}
                </span>
            </div>

            {{-- Tabs (segmented pill) --}}
            <div class="inline-flex items-center gap-1 p-1 bg-gray-100 dark:bg-slate-700/60 rounded-xl mt-5">
                @php
                    $tabBtn = "inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition focus:outline-none whitespace-nowrap";
                    $tabOn  = "bg-white dark:bg-slate-800 text-blue-600 shadow-sm";
                    $tabOff = "text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200";
                @endphp
                <button @click="tab = 'project'" :class="tab === 'project' ? '{{ $tabOn }}' : '{{ $tabOff }}'" class="{{ $tabBtn }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    {{ __('Project') }}
                </button>
                <button @click="tab = 'commercial'" :class="tab === 'commercial' ? '{{ $tabOn }}' : '{{ $tabOff }}'" class="{{ $tabBtn }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
                    {{ __('Commercial') }}
                </button>
                <button @click="tab = 'order'" :class="tab === 'order' ? '{{ $tabOn }}' : '{{ $tabOff }}'" class="{{ $tabBtn }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ __('Order') }}
                </button>
                <button @click="tab = 'todo'" :class="tab === 'todo' ? '{{ $tabOn }}' : '{{ $tabOff }}'" class="{{ $tabBtn }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7l2 2 4-4"/></svg>
                    {{ __('To-do List') }}
                </button>
            </div>
        </div>
    </header>

    <div class="w-full px-4 sm:px-6 lg:px-8 py-6">

        {{-- Submitted banner --}}
        @if(strtolower($project->status) === 'submit')
            <div class="mb-6 flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 px-4 py-3">
                <svg class="h-5 w-5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm">
                    <span class="font-semibold text-blue-800 dark:text-blue-200">{{ __('Submitted') }}</span>
                    <span class="text-blue-700 dark:text-blue-300">{{ __('This project has been submitted and is waiting for approval.') }}</span>
                </div>
            </div>
        @endif

        {{-- Project tab: detail + progress + customer/clients/agent --}}
        <div x-show="tab === 'project'" class="bg-white dark:bg-slate-700 shadow rounded-lg p-4 sm:p-6">
            <div class="md:grid md:grid-cols-5 md:gap-6">
                <div class="md:col-span-12 lg:col-span-4">
                    @livewire('project.edit', ['uuid'=>$project->id])
                </div>
                <div class="justify-between lg:visible md:invisible">
                    @livewire('project.progress', ['uuid'=>$project->id])
                </div>
            </div>
        </div>

        {{-- Commercial tab: project-wide overview. Per-client create lives in the Project tab's Clients table. --}}
        <div x-show="tab === 'commercial'" class="bg-white dark:bg-slate-700 shadow rounded-lg overflow-hidden" style="display:none;">
            <div class="px-4 pt-4">
                @php
                    $subBtn = "inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-sm font-medium transition focus:outline-none whitespace-nowrap";
                    $subOn  = "bg-white dark:bg-slate-800 text-blue-600 shadow-sm";
                    $subOff = "text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200";
                @endphp
                <div class="inline-flex items-center gap-1 p-1 bg-gray-100 dark:bg-slate-700/60 rounded-xl">
                    <button type="button" @click="sub = 'product'" :class="sub === 'product' ? '{{ $subOn }}' : '{{ $subOff }}'" class="{{ $subBtn }}">{{ __('Product Master Data') }}</button>
                    <button type="button" @click="sub = 'quotation'" :class="sub === 'quotation' ? '{{ $subOn }}' : '{{ $subOff }}'" class="{{ $subBtn }}">{{ __('Quotation') }}</button>
                    <button type="button" @click="sub = 'contract'" :class="sub === 'contract' ? '{{ $subOn }}' : '{{ $subOff }}'" class="{{ $subBtn }}">{{ __('Contract') }}</button>
                </div>
            </div>

            {{-- Product Master Data --}}
            <div x-show="sub === 'product'" class="p-4 space-y-3">
                <div class="flex justify-end">
                    @livewire('commercial.item.add')
                </div>
                <livewire:table.commerce-item searchable="name, sku, type" exportable :key="'pm-tbl-'.$project->id" />
            </div>

            {{-- Quotation: client list -> View Quotation per client (opens modal) --}}
            <div x-show="sub === 'quotation'" class="p-4 space-y-3" style="display:none;">
                <p class="text-xs text-gray-400">{{ __('Pick a client to view or create its quotations.') }}</p>
                <livewire:table.project-client-commercial :project_id="$project->id" only="quotation"
                    searchable="name, sender, phone, email" exportable :key="'pcc-q-'.$project->id" />
            </div>

            {{-- Contract: client list -> View Contract per client (opens modal) --}}
            <div x-show="sub === 'contract'" class="p-4 space-y-3" style="display:none;">
                <p class="text-xs text-gray-400">{{ __('Pick a client to view or create its contracts.') }}</p>
                <livewire:table.project-client-commercial :project_id="$project->id" only="contract"
                    searchable="name, sender, phone, email" exportable :key="'pcc-c-'.$project->id" />
            </div>
        </div>

        {{-- Single shared modal for per-client quotation/contract (page level so it works on any tab) --}}
        @livewire('project.client-commercial', ['id' => $project->id, 'showTable' => false], key('client-commercial-modal-'.$project->id))

        {{-- Order tab: order list --}}
        <div x-show="tab === 'order'" class="bg-white dark:bg-slate-700 shadow rounded-lg p-4 space-y-6" style="display:none;">
            <div class="flex justify-end gap-2">
                @livewire('order.add', ['source' => $project->id, 'model' => 'PROJECT'])
            </div>
            @livewire('project.orders', ['id' => $project->id])
        </div>

        {{-- To-do tab --}}
        <div x-show="tab === 'todo'" class="bg-white dark:bg-slate-700 shadow rounded-lg p-4 sm:p-6" style="display:none;">
            @livewire('task.todo', ['id'=>$project->id])
        </div>

    </div>
    </div>

</x-app-layout>
