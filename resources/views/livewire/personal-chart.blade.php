<div>
    <div class="container mx-auto space-y-2 p-1 sm:p-0">
        <!-- <ul class="flex flex-col sm:flex-row sm:space-x-8 sm:items-center">
            <li>
                <input type="checkbox" value="travel" wire:model="types" />
                <span>Travel</span>
            </li>
            <li>
                <input type="checkbox" value="shopping" wire:model="types" />
                <span>Shopping</span>
            </li>
            <li>
                <input type="checkbox" value="food" wire:model="types" />
                <span>Food</span>
            </li>
            <li>
                <input type="checkbox" value="entertainment" wire:model="types" />
                <span>Entertainment</span>
            </li>
            <li>
                <input type="checkbox" value="other" wire:model="types" />
                <span>Other</span>
            </li>
        </ul> -->
        <!-- <div class="shadow rounded p-4 border bg-white flex-1" style="height: 32rem;">
                <livewire:livewire-column-chart key="{{ $columnChartModel->reactiveKey() }}" :column-chart-model="$columnChartModel" />
            </div> -->
        <div class="shadow rounded p-2 border bg-white" style="height:19.9rem;">
            <livewire:livewire-line-chart key="{{ $lineChartModel->reactiveKey() }}" :line-chart-model="$lineChartModel" />
        </div>
        <!-- <div class="shadow rounded p-4 border bg-white" style="height: 32rem;">
            <livewire:livewire-line-chart
                key="{{ $multiLineChartModel->reactiveKey() }}"
                :line-chart-model="$multiLineChartModel"
            />
        </div> -->
    </div>

    @push('charts')
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush
</div>
