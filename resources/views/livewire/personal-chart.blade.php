<div>
    <div class="container mx-auto space-y-2 p-1 sm:p-0">
        <div class="shadow rounded p-4 border bg-white" style="height: 32rem;">
        @if($multiLineChartModel)
            <livewire:livewire-line-chart
                key="{{ $multiLineChartModel->reactiveKey() }}"
                :line-chart-model="$multiLineChartModel"
            />
        @endif
        </div>
    </div>

    @push('charts')
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush
</div>
