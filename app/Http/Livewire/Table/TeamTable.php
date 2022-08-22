<?php

namespace App\Http\Livewire\Table;

use App\Models\Team;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class TeamTable extends LivewireDatatable
{
    public $model = Team::class;

    public function builder()
    {
        return Team::query()->groupBy('user_id');
    }

    public function columns()
    {
        return [
    		Column::name('user.name')->filterable()->label('Name'),
    		Column::name('user.email')->filterable()->label('Email'),
    		DateColumn::name('created_at')->label('Register Date'),
            NumberColumn::name('user_id')->label('Detail')->sortBy('user_id')->callback('user_id', function ($value) {
                return view('datatables::link', [
                    'href' => "/user/" . $value . '?month='.date('m').'&year='.date('Y'),
                    'slot' => 'View'
                ]);
            }),
    	];
    }
}
