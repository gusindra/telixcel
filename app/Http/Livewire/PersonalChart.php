<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Request;
use Asantibanez\LivewireCharts\Models\AreaChartModel;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Asantibanez\LivewireCharts\Models\MultiLineChartModel;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PersonalChart extends Component
{

    public $types = ['text', 'shopping', 'entertainment', 'travel', 'other'];
    public $colors = [
        'text' => '#f6ad55',
        'shopping' => '#fc8181',
        'entertainment' => '#90cdf4',
        'travel' => '#66DA26',
        'other' => '#cbd5e0',
    ];
    public $showDataLabels = false;
    public $firstRun = true;
    protected $listeners = [
        'onPointClick' => 'handleOnPointClick',
        'onSliceClick' => 'handleOnSliceClick',
        'onColumnClick' => 'handleOnColumnClick',
    ];

    public function handleOnPointClick($point)
    {
        dd($point);
    }
    public function handleOnSliceClick($slice)
    {
        dd($slice);
    }
    public function handleOnColumnClick($column)
    {
        dd($column);
    }

    public function render()
    {
        if(auth()->user()->currentTeam && auth()->user()->currentTeam->requestAll){
            $expenses = auth()->user()->currentTeam->requestAll->where('user_id', auth()->user()->id);
            // $columnChartModel =
            // (new ColumnChartModel())
            //     ->setTitle('Expenses by Type')
            //     ->addColumn('Food', 100, '#f6ad55')
            //     ->addColumn('Shopping', 200, '#fc8181')
            //     ->addColumn('Travel', 300, '#90cdf4');
            // ;
            // dd($expenses->groupBy('from'));
            $expenses = $expenses->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('dayofWeek'); // dayofWeek
            })->first();
            // dd($expenses);
            if($expenses){
                $columnChartModel = $expenses->reduce(
                        function ($columnChartModel, $data) {
                            $type = $data->first()->type;
                            $value = $data->count();
                            if ( in_array( $type,  ['text'] )){
                                $date_col = "07 Jun"; //$data->first()->created_at->format("d M")
                                $value_col = 40; //$value
                                $color_col = '#f6ad55'; //$this->colors[$type];
                                $data_col = array(1,2,3); //$data;
                                if($date_col!=null && $value_col!=null && $data!=null && $color_col!=null){
                                    // return $columnChartModel->addColumn($date_col, $value_col, $color_col);
                                }
                            }
                            // return $columnChartModel->addColumn('Travel', 300, '#90cdf4');
                        },
                        LivewireCharts::columnChartModel()
                            ->setTitle('Request by Type')
                            ->setAnimated($this->firstRun)
                            ->withOnColumnClickEventName('onColumnClick')
                );

                $lineChartModel = $expenses->groupBy(function($date) {
                        return Carbon::parse($date->created_at)->format('dayofWeek'); // dayofWeek
                    })->reduce(
                        function ($lineChartModel, $data) {
                            $date = $data->first()->created_at->format("d M Y");
                            $amountSum = $data->count();
                            // if ($index == 6) {
                            //     $lineChartModel->addMarker(7, $amountSum);
                            // }
                            // if ($index == 11) {
                            //     $lineChartModel->addMarker(12, $amountSum);
                            // }
                            return $lineChartModel->addPoint($data->first()->created_at->format("d M"), $amountSum, ['id' => $data]);
                        },
                        LivewireCharts::lineChartModel()
                            ->setTitle('Direct Chat')
                            ->setAnimated($this->firstRun)
                            ->withOnPointClickEvent('onPointClick')
                    );

                $multiLineChartModel = $expenses->groupBy(function($date) {
                    return Carbon::parse($date->created_at)->format('dayofWeek'); // dayofWeek
                }, 'from')->reduce(function ($multiLineChartModel, $data) use ($expenses) {
                    $index = $expenses->search($data);
                    $date = $data->first()->created_at->format("d M Y");
                    $amountSum = $data->count();
                    $type = $data->first()->from != 'bot' ? 'User':'Bot';
                    // $defaultArr = array(
                    //     'bot' => 0,
                    //     'user' => 0,
                    //     'agent' => 0
                    // );
                    $dataArr = $data->reduce(function($array, $request){
                        if($request->from=='bot'){
                            $array['BOT'] = @$array['BOT'] ? $array['BOT'] + 1:1;
                        }elseif($request->from=='api'){
                            $array['API'] = @$array['API'] ? $array['API'] + 1:1;
                        }elseif($request->source_id!=null){
                            $array['Client'] =  @$array['Client'] ? $array['Client'] + 1:1;
                        }else{
                            $array['Agent'] = @$array['Agent'] ? $array['Agent'] + 1:1;
                        }
                        return $array;
                    });
                    // dd($dataArr);
                    foreach($dataArr as $key => $req){
                        $multiLineChartModel = $multiLineChartModel->addSeriesPoint($key, $data->first()->created_at->format("d M"),  $req,  ['id' => $data]);
                    }
                    return $multiLineChartModel;
                }, LivewireCharts::multiLineChartModel()
                    ->setTitle('Request by Type')
                    ->setAnimated($this->firstRun)
                    ->withOnPointClickEvent('onPointClick')
                    ->setSmoothCurve()
                    ->multiLine()
                    ->setDataLabelsEnabled($this->showDataLabels)
                    // ->addSeriesPoint('A', '1 Jul', 10, ['id' => 1])
                    // ->addSeriesPoint('B', '1 Jul', 20, ['id' => 1])
                    // ->addSeriesPoint('A', '2 Jul', 320, ['id' => 2])
                    // ->addSeriesPoint('B', '2 Jul', 40, ['id' => 2])
                    // ->addSeriesPoint('A', '3 Jul', 50, ['id' => 3])
                    // ->addSeriesPoint('B', '3 Jul', 60, ['id' => 3])
                    // ->addSeriesPoint('A', '4 Jul', 70, ['id' => 4])
                    // ->addSeriesPoint('B', '4 Jul', 80, ['id' => 4])
                    //->setColors(['#b01a1b', '#f66665', '#000000'])
                );
            }

        }

        $this->firstRun = false;
        return view('livewire.personal-chart')
            ->with([
                'columnChartModel' => $columnChartModel ?? false,
                'lineChartModel' => $lineChartModel ?? false,
                'multiLineChartModel' => $multiLineChartModel ?? false,
            ]);
    }
}
