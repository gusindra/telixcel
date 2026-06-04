@php
    $btnPrimary = 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition';
    $btnSecondary = 'inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition';
    $inputCls = 'border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full text-sm';
    $labelCls = 'block font-medium text-sm text-gray-700 dark:text-slate-300';
@endphp
<div>
    {{-- ── L1: client datatable (optional; hidden in modal-only mode) ── --}}
    @if($showTable)
        <livewire:table.project-client-commercial :project_id="$project_id"
            searchable="name, sender, phone, email" exportable :key="'pcc-table-'.$project_id" />
    @endif

    {{-- ── Single modal; content switches by $step ─────────────────── --}}
    @if($step !== '')
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500/75" wire:click="close"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                @php
                    $isView = in_array($step, ['quotation-view','contract-view']);
                    $isList = in_array($step, ['quotation-list','contract-list']);
                @endphp
                <div class="relative w-full {{ $isView ? '' : ($isList ? 'max-w-5xl' : (in_array($step, ['quotation-create']) ? 'max-w-2xl' : 'max-w-md')) }} rounded-lg bg-white dark:bg-slate-700 shadow-xl"
                     @if($isView) style="max-width:96vw; width:96vw;" @endif>

                    {{-- header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-slate-600">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-slate-200">
                            @switch($step)
                                @case('quotation-list') {{ __('Quotations') }} @break
                                @case('contract-list') {{ __('Contracts') }} @break
                                @case('quotation-create') {{ __('New Quotation') }} @break
                                @case('contract-create') {{ __('New Contract') }} @break
                                @case('quotation-view') {{ __('Quotation') }}: <span class="text-gray-500">{{ $viewTitle }}</span> @break
                                @case('contract-view') {{ __('Contract') }}: <span class="text-gray-500">{{ $viewTitle }}</span> @break
                            @endswitch
                            — <span class="capitalize text-gray-500 dark:text-slate-400">{{ $clientName }}</span>
                        </h3>
                        <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- body --}}
                    <div class="px-6 py-5">
                        {{-- quotation list (datatable, scoped to this client) --}}
                        @if($step === 'quotation-list')
                            <div class="flex justify-end mb-3">
                                <button type="button" wire:click="openCreate('quotation')" class="{{ $btnPrimary }}">{{ __('+ New Quotation') }}</button>
                            </div>
                            <div class="overflow-x-auto">
                                <livewire:table.quotation :project_id="$project_id" :client_id="$clientId" :emit-view="true"
                                    searchable="title, status" :key="'modal-q-'.$clientId" />
                            </div>

                        {{-- contract list (datatable, scoped to this client) --}}
                        @elseif($step === 'contract-list')
                            <div class="flex justify-end mb-3">
                                <button type="button" wire:click="openCreate('contract')" class="{{ $btnPrimary }}">{{ __('+ New Contract') }}</button>
                            </div>
                            <div class="overflow-x-auto">
                                <livewire:table.contract :project_id="$project_id" :client_id="$clientId" :emit-view="true"
                                    searchable="title, status" :key="'modal-c-'.$clientId" />
                            </div>

                        {{-- create quotation --}}
                        @elseif($step === 'quotation-create')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="{{ $labelCls }}">{{ __('Type') }}</label>
                                    <select wire:model="q_type" class="{{ $inputCls }}">
                                        <option value="">-- {{ __('Select') }} --</option>
                                        <option value="project">Project base</option>
                                        <option value="product">Products</option>
                                        <option value="price">Price &amp; Discount</option>
                                        <option value="term">Annexed Commercial Terms</option>
                                    </select>
                                    @error('q_type') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">{{ __('Title') }}</label>
                                    <input type="text" wire:model.defer="q_title" class="{{ $inputCls }}" />
                                    @error('q_title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">{{ __('Quotation Date') }}</label>
                                    <input type="date" wire:model.defer="q_date" class="{{ $inputCls }}" />
                                    @error('q_date') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="{{ $labelCls }}">{{ __('Duration') }}</label>
                                    <select wire:model="q_valid_day" class="{{ $inputCls }}">
                                        <option value="3">3 days</option><option value="7">7 days</option><option value="30">30 days</option><option value="60">60 days</option>
                                    </select>
                                    @error('q_valid_day') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <p class="md:col-span-2 text-xs text-gray-400">{{ __('Product items follow the latest quotation of this client. Edit items after opening the quotation.') }}</p>
                            </div>

                        {{-- create contract --}}
                        @elseif($step === 'contract-create')
                            <div>
                                <label class="{{ $labelCls }}">{{ __('Title') }}</label>
                                <input type="text" wire:model.defer="c_title" class="{{ $inputCls }}" />
                                @error('c_title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                        {{-- view (iframe, navbar hidden via embed=1) --}}
                        @elseif(in_array($step, ['quotation-view','contract-view']))
                            @if($viewUrl)
                                <iframe src="{{ $viewUrl }}" style="height:82vh;" class="w-full rounded-md border border-gray-200 dark:border-slate-600 bg-white"
                                        title="{{ $viewTitle }}"></iframe>
                            @endif
                        @endif
                    </div>

                    {{-- footer --}}
                    <div class="flex justify-end gap-2 px-6 py-4 bg-gray-50 dark:bg-slate-800 rounded-b-lg">
                        @if($step === 'quotation-list' || $step === 'contract-list')
                            <button type="button" wire:click="close" class="{{ $btnSecondary }}">{{ __('Close') }}</button>
                        @elseif($step === 'quotation-create')
                            <button type="button" wire:click="back('quotation-list')" class="{{ $btnSecondary }}">{{ __('Back') }}</button>
                            <button type="button" wire:click="createQuotation" wire:loading.attr="disabled" class="{{ $btnPrimary }}">{{ __('Save Quotation') }}</button>
                        @elseif($step === 'contract-create')
                            <button type="button" wire:click="back('contract-list')" class="{{ $btnSecondary }}">{{ __('Back') }}</button>
                            <button type="button" wire:click="createContract" wire:loading.attr="disabled" class="{{ $btnPrimary }}">{{ __('Save Contract') }}</button>
                        @elseif($step === 'quotation-view' || $step === 'contract-view')
                            <a href="{{ $viewUrl }}" target="_blank" class="{{ $btnSecondary }}">{{ __('Open in new tab') }}</a>
                            <button type="button" wire:click="back('{{ $step === 'quotation-view' ? 'quotation-list' : 'contract-list' }}')" class="{{ $btnPrimary }}">{{ __('Back') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
