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
        $expenses = auth()->user()->currentTeam->requestAll->where('from', auth()->user()->id);
        // $columnChartModel =
        // (new ColumnChartModel())
        //     ->setTitle('Expenses by Type')
        //     ->addColumn('Food', 100, '#f6ad55')
        //     ->addColumn('Shopping', 200, '#fc8181')
        //     ->addColumn('Travel', 300, '#90cdf4')
        // ;
        $columnChartModel = $expenses->groupBy('type')->reduce(
                function ($columnChartModel, $data) {
                    $type = $data->first()->type;
                    $value = $data->count();
                    return $columnChartModel->addColumn($type, $value, $this->colors[$type]);
                },
                LivewireCharts::columnChartModel()
                    ->setTitle('Request by Type')
                    ->setAnimated($this->firstRun)
                    ->withOnColumnClickEventName('onColumnClick')
            );
        $lineChartModel = $expenses->where('source_id', NULL)->where('from', auth()->user()->id)->groupBy(function($date) {
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
        $multiLineChartModel = $expenses
            ->reduce(function ($multiLineChartModel, $data) use ($expenses) {
                $index = $expenses->search($data);

                return $multiLineChartModel
                    ->addSeriesPoint($data->type, $index, $data->amount,  ['id' => $data->id]);
            }, LivewireCharts::multiLineChartModel()
                ->setTitle('Expenses by Type')
                ->setAnimated($this->firstRun)
                ->withOnPointClickEvent('onPointClick')
                ->setSmoothCurve()
                ->multiLine()
                // ->setDataLabelsEnabled($this->showDataLabels)
                ->addSeriesPoint('A', 'aaa', 1, ['id' => 1])
                ->addSeriesPoint('B', 'bbb', 2, ['id' => 2])
                ->addSeriesPoint('A', 'ccc', 3, ['id' => 3])
                ->addSeriesPoint('B', 'ddd', 2, ['id' => 4])
                ->setColors(['#b01a1b', '#f66665', '#000000'])
            );
        $this->firstRun = false;
        return view('livewire.personal-chart')
            ->with([
                'columnChartModel' => $columnChartModel,
                'lineChartModel' => $lineChartModel,
                'multiLineChartModel' => $multiLineChartModel,
            ]);
    }
}
