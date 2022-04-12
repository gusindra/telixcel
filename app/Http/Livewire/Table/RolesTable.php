<?php

namespace App\Http\Livewire\Table;

use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use App\Models\Role;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class RolesTable extends LivewireDatatable
{
    public $model = Role::class;

    public function builder()
    {
        return Role::query();
    }

    public function columns()
    {
        return [
    		Column::name('name')->label('Name'),
    		Column::name('description')->label('Description'),
    		Column::name('type')->label('Type'),
            BooleanColumn::name('status')->label('Active'),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/roles/" . $value . '?month='.date('m').'&year='.date('Y'),
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
