<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Project') }}</h2>
    </x-slot>

    <div x-data="{ tab: 'project', sub: 'product' }">
    <nav class="tx-subnav" aria-label="{{ __('Project') }}">
        <div class="tx-subnav-track">
            <button type="button" class="tx-tab" :class="tab === 'project' && 'tx-tab-active'" @click="tab = 'project'">{{ __('Project') }}</button>
            <button type="button" class="tx-tab" :class="tab === 'commercial' && 'tx-tab-active'" @click="tab = 'commercial'">{{ __('Commercial') }}</button>
            <button type="button" class="tx-tab" :class="tab === 'contract' && 'tx-tab-active'" @click="tab = 'contract'">{{ __('Contract') }}</button>
            <button type="button" class="tx-tab" :class="tab === 'order' && 'tx-tab-active'" @click="tab = 'order'">{{ __('Order') }}</button>
            <button type="button" class="tx-tab" :class="tab === 'todo' && 'tx-tab-active'" @click="tab = 'todo'">{{ __('To-do List') }}</button>
        </div>
        <div class="tx-subnav-meta" title="{{ $project->name }}">
            <span class="tx-subnav-meta-name capitalize">{{ $project->name }}</span>
        </div>
    </nav>

    <div class="tx-stack">

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

        </div>

        {{-- Single shared modal for per-client quotation/contract (page level so it works on any tab) --}}
        @livewire('project.client-commercial', ['id' => $project->id, 'showTable' => false], key('client-commercial-modal-'.$project->id))

        {{-- Contract tab: project-level contracts (not tied to a client), styled like Order --}}
        <div x-show="tab === 'contract'" class="bg-white dark:bg-slate-700 shadow rounded-lg p-4 space-y-6" style="display:none;">
            <div class="flex justify-end gap-2">
                @livewire('project.contract-add', ['source' => $project->id, 'model' => 'PROJECT'], key('contract-add-'.$project->id))
            </div>
            <livewire:table.contract :project_id="$project->id" searchable="title" exportable :key="'contract-tbl-'.$project->id" />
        </div>

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
