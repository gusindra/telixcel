<div class="tx-card ai-usage-filters" wire:key="usage-details">
    <div>
        <x-jet-label value="Model" />
        <x-select class="mt-1" wire:model="usageModel">
            <option value="">{{ __('All') }}</option>
            @foreach($seenModels as $id)
                <option value="{{ $id }}">{{ $id }}</option>
            @endforeach
        </x-select>
    </div>
    <div>
        <x-jet-label value="Customer" />
        <x-jet-input type="text" class="mt-1 block w-full" wire:model.debounce.400ms="endUser" placeholder="Budi" />
    </div>
</div>

<div class="tx-card">
    <h3 class="text-base font-semibold tx-fg mt-0 mb-1">{{ __('By model') }}</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left tx-fg-muted">
                <tr>
                    <th class="px-3 py-2">{{ __('Model') }}</th>
                    <th class="px-3 py-2">{{ __('Calls') }}</th>
                    <th class="px-3 py-2">{{ __('Input') }}</th>
                    <th class="px-3 py-2">{{ __('Output') }}</th>
                    <th class="px-3 py-2">{{ __('Tokens') }}</th>
                    <th class="px-3 py-2">{{ __('Cost') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byModel as $row)
                    <tr style="box-shadow: inset 0 -1px 0 var(--tx-border);">
                        <td class="px-3 py-2 font-mono text-xs tx-fg">{{ $row->model }}</td>
                        <td class="px-3 py-2">{{ number_format($row->usage_rows) }}</td>
                        <td class="px-3 py-2">{{ number_format($row->input_tokens) }}</td>
                        <td class="px-3 py-2">{{ number_format($row->output_tokens) }}</td>
                        <td class="px-3 py-2">{{ number_format($row->total_tokens) }}</td>
                        <td class="px-3 py-2">{{ $this->formatMoney($row->cost) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-6 text-center tx-fg-muted">{{ __('No model usage in this range.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('ai.usage-pager', ['pager' => $modelPager, 'method' => 'gotoModelPage'])
</div>

@if($isGlobal)
    <div class="tx-card">
        <h3 class="text-base font-semibold tx-fg mt-0 mb-1">{{ __('By application') }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left tx-fg-muted">
                    <tr>
                        <th class="px-3 py-2">{{ __('Application') }}</th>
                        <th class="px-3 py-2">{{ __('Calls') }}</th>
                        <th class="px-3 py-2">{{ __('Tokens') }}</th>
                        <th class="px-3 py-2">{{ __('Cost') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byApplication as $row)
                        <tr style="box-shadow: inset 0 -1px 0 var(--tx-border);">
                            <td class="px-3 py-2 font-medium tx-fg">{{ $row->application_name }}</td>
                            <td class="px-3 py-2">{{ number_format($row->usage_rows) }}</td>
                            <td class="px-3 py-2">{{ number_format($row->total_tokens) }}</td>
                            <td class="px-3 py-2">{{ $this->formatMoney($row->cost) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-6 text-center tx-fg-muted">{{ __('No application usage in this range.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('ai.usage-pager', ['pager' => $appPager, 'method' => 'gotoAppPage'])
    </div>
@endif

<div class="tx-card">
    <h3 class="text-base font-semibold tx-fg mt-0 mb-1">{{ $isGlobal ? __('Customers') : __('Customers inside this app') }}</h3>
    <p class="text-sm tx-fg-muted mt-0 mb-3">{{ __('Requires metadata.end_user_id from the client.') }}</p>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left tx-fg-muted">
                <tr>
                    <th class="px-3 py-2">{{ __('Customer') }}</th>
                    <th class="px-3 py-2">{{ __('ID') }}</th>
                    <th class="px-3 py-2">{{ __('Calls') }}</th>
                    <th class="px-3 py-2">{{ __('Tokens') }}</th>
                    <th class="px-3 py-2">{{ __('Cost') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byUser as $row)
                    <tr style="box-shadow: inset 0 -1px 0 var(--tx-border);">
                        <td class="px-3 py-2 font-medium tx-fg">{{ $row->end_user_name ?: '—' }}</td>
                        <td class="px-3 py-2 font-mono text-xs">{{ $row->end_user_id }}</td>
                        <td class="px-3 py-2">{{ number_format($row->usage_rows) }}</td>
                        <td class="px-3 py-2">{{ number_format($row->total_tokens) }}</td>
                        <td class="px-3 py-2">{{ $this->formatMoney($row->cost) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center tx-fg-muted">{{ __('No customer usage yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('ai.usage-pager', ['pager' => $userPager, 'method' => 'gotoUserPage'])
</div>
