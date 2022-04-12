<?php

namespace App\Http\Livewire\Table;

use App\Models\Project;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class ProjectTable extends LivewireDatatable
{
    public $model = Project::class;

    public function columns()
    {
        return [
    		Column::name('name')->label('Name'),
    		Column::name('customer_name')->label('Customer'),
    		Column::name('type')->label('Type'),
            Column::callback(['status'], function ($type) {
                return view('assistant.project.label', ['type' => $type]);
            })->label('Status'),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/project/" . $value . '?month='.date('m').'&year='.date('Y'),
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
