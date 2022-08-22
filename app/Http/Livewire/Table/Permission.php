<?php

namespace App\Http\Livewire\Table;

use App\Models\Permission as ModelsPermission;
use Illuminate\Support\Str;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class Permission extends LivewireDatatable
{
    public $model = ModelsPermission::class;

    public function builder()
    {
        return ModelsPermission::query();
    }

    public function columns()
    {
        return [
    		Column::name('name')->label('Name'),
    		Column::callback('model', function ($value) {
                    return view('datatables::link', [
                        'href' => "/flow/" . Str::lower($value),
                        'slot' => $value
                    ]);
                })->label('Model'),
            // NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
            //     return view('datatables::link', [
            //         'href' => "/roles/" . $value . '?month='.date('m').'&year='.date('Y'),
            //         'slot' => 'View'
            //     ]);
            // }),

    	];
    }
}
