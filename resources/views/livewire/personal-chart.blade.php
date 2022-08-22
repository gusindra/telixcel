<div>
    <div>
        <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="py-2 sm:px-2 bg-opacity-10 grid grid-cols-1 md:grid-cols-1 gap-3">
                <div class="container mx-auto space-y-2 p-1 sm:p-0">
                    <div class="shadow rounded p-4 border bg-gray-300" style="height: 32rem;">
                        @if($multiLineChartModel)
                        <livewire:livewire-line-chart key="{{ $multiLineChartModel->reactiveKey() }}" :line-chart-model="$multiLineChartModel" />
                        @endif
                    </div>
                </div>

                @push('charts')
                <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                @endpush
            </div>
        </div>
    </div>
    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('Raw Data') }} {{  @$dataRes['seriesName'] }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <div>
                    <table class="table table-striped border w-full text-center">
                        <tr>
                            <td class="border">Date</td>
                            <td class="border">Total</td>
                        </tr>
                        <tr>
                            <td class="border">{{  @$dataRes['title'] }}</td>
                            <td class="border">{{  @$dataRes['value'] }}</td>
                        </tr>
                    </table>

                    @if(key_exists("extras", $dataRes))
                        @forelse ($dataRes['extras'] as $datas)
                            @foreach ($datas as $data)
                                <table class="border">
                                @if(@$dataRes['seriesName']=='BOT' && $data['from']==strtolower($dataRes['seriesName']))
                                    @foreach ($data as $k => $d)
                                        @if($d!='' && $d!=0)
                                            <tr>
                                                <td class="p-2">{{$k}}</td>
                                                <td class="w-full p-2">{{is_array($d) ? var_dump($d) : $d}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @elseif(@$dataRes['seriesName']=='Client' && $data['from']!='bot' && $data['source_id']!='')
                                    @foreach ($data as $k => $d)
                                        @if($d!='' && $d!=0)
                                            <tr>
                                                <td class="p-2">{{$k}}</td>
                                                <td class="w-full p-2">{{is_array($d) ? var_dump($d) : $d}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @elseif(@$dataRes['seriesName']=='Agent' && $data['from']!='bot' && $data['source_id']=='')
                                    @foreach ($data as $k => $d)
                                        @if($d!='' && $d!=0 && $k!='pivot')
                                            <tr>
                                                <td class="p-2">{{$k}}</td>
                                                <td class="w-full p-2">{{is_array($d) ? var_dump($d) : $d}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </table>
                            @endforeach
                        @empty
                        <p>No Data</p>
                        @endforelse
                    @else
                        <p>No Data</p>
                    @endif

                </div>
                <x-jet-input-error for="type" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalActionVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
