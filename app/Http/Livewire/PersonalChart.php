<?php

namespace App\Http\Livewire;

use App\Models\BlastMessage;
use Livewire\Component;
use App\Models\Request;
use Asantibanez\LivewireCharts\Models\AreaChartModel;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Asantibanez\LivewireCharts\Models\MultiLineChartModel;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PersonalChart extends Component
{
    public $dataRes = array();
    public $modalActionVisible = false;
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
        // dd($point);
        $this->dataRes = $point;
        $this->modalActionVisible = true;
    }
    public function handleOnSliceClick($slice)
    {
        $this->dataRes = $slice;
        $this->modalActionVisible = true;
        // dd($slice);
    }
    public function handleOnColumnClick($column)
    {
        $this->dataRes = $column;
        $this->modalActionVisible = true;
        // dd($column);
    }

    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function render()
    {
        $columnChartModel = null;
        $lineChartModel = null;
        $multiLineChartModel = null;
        if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
            $expenses = Request::query();
        }else{
            if(auth()->user()->currentTeam->user_id == auth()->user()->id){
                $expenses = Request::select('source_id', 'from', 'user_id', 'created_at')->where('user_id', auth()->user()->id)->groupBy(function($date) {
                    return Carbon::parse($date->created_at)->format('dayofWeek'); // dayofWeek
                }, 'from')->get()->toArray();
            }else{
                //auth()->user()->currentTeam->requestAll
                $expenses = auth()->user()->currentTeam->requestAll->where('user_id', auth()->user()->id)->groupBy(function($date) {
                    return Carbon::parse($date->created_at)->format('dayofWeek'); // dayofWeek
                }, 'from');
            }
        }
        // $columnChartModel = $expenses->groupBy(function($date) {
        //     return Carbon::parse($date->created_at)->format('dayofWeek'); // dayofWeek
        // })->reduce(
        //     function ($columnChartModel, $data) {
        //         $type = $data->first()->type;
        //         $value = $data->count();
        //         // dd($data);
        //         if($type!='document' && $type!='image'){
        //             // return $columnChartModel->addColumn($data->first()->created_at->format("d M"), $value, $this->colors[$type], ['id' => $data]);
        //         }
        //     },
        //     LivewireCharts::columnChartModel()
        //         ->setTitle('Request by Type')
        //         ->setAnimated($this->firstRun)
        //         ->withOnColumnClickEventName('onColumnClick')
        // );

        // $lineChartModel = $expenses->groupBy(function($date) {
        //     return Carbon::parse($date->created_at)->format('dayofWeek'); // dayofWeek
        // })->reduce(
        //     function ($lineChartModel, $data) {
        //         $date = $data->first()->created_at->format("d M Y");
        //         $amountSum = $data->count();
        //         // if ($index == 6) {
        //         //     $lineChartModel->addMarker(7, $amountSum);
        //         // }
        //         // if ($index == 11) {
        //         //     $lineChartModel->addMarker(12, $amountSum);
        //         // }
        //         return $lineChartModel->addPoint($data->first()->created_at->format("d M"), $amountSum, ['id' => $data]);
        //     },
        //     LivewireCharts::lineChartModel()
        //         ->setTitle('Direct Chat')
        //         ->setAnimated($this->firstRun)
        //         ->withOnPointClickEvent('onPointClick')
        // );
        // dd($expenses);
        $multiLineChartModel = $expenses->reduce(function ($multiLineChartModel, $data) use ($expenses) {
            // $index = $expenses->search($data);
            // $date = $data->first()->created_at->format("d M Y");
            // $amountSum = $data->count();
            // $type = $data->first()->from != 'bot' ? 'User':'Bot';
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
            $sms = BlastMessage::where('user_id', auth()->user()->id)->count();
            // foreach($){

            // }
            $dataArr['SMS'] = 40;
            // dd($dataArr);
            foreach($dataArr as $key => $req){
                $multiLineChartModel = $multiLineChartModel->addSeriesPoint($key, $data->first()->created_at->format("d M"),  $req,  ['id' => $data]);
                // $multiLineChartModel = $multiLineChartModel->addSeriesPoint('API', $data->first()->created_at->format("d M"), 20,  ['id' => $data]);
            }
            // dd($data->last()->created_at->format("d M"));
            return $multiLineChartModel;
        }, LivewireCharts::multiLineChartModel()
            ->setTitle('Request by Type')
            ->setAnimated($this->firstRun)
            ->withOnPointClickEvent('onPointClick')
            ->setSmoothCurve()
            ->multiLine()
            ->setDataLabelsEnabled($this->showDataLabels)
            // ->addSeriesPoint('B', '1 Jul', 20, ['id' => 1])
            // ->addSeriesPoint('A', '2 Jul', 320, ['id' => 2])
            // ->addSeriesPoint('B', '2 Jul', 40, ['id' => 2])
            // ->addSeriesPoint('A', '3 Jul', 50, ['id' => 3])
            // ->addSeriesPoint('B', '3 Jul', 60, ['id' => 3])
            // ->addSeriesPoint('A', '4 Jul', 70, ['id' => 4])
            // ->addSeriesPoint('B', '4 Jul', 80, ['id' => 4])
            // ->setColors(['#b01a1b', '#f66665', '#000000'])
        );
        // dd($multiLineChartModel);

        $this->firstRun = false;

        return view('livewire.personal-chart')->with([
                    // 'columnChartModel' => $columnChartModel,
                    // 'lineChartModel' => $lineChartModel,
                    'multiLineChartModel' => $multiLineChartModel,
                ]);
    }
}
