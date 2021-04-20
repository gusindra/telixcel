<?php

namespace App\Http\Livewire;

use App\Models\Request;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class RequestsTable extends LivewireDatatable
{
    public $model = Request::class;

    public function columns()
    {
        return [
    		NumberColumn::name('id')->label('ID')->sortBy('id'),
    		Column::name('user_id')->label('User'),
    		Column::name('type')->label('Type'),
    		DateColumn::name('created_at')->label('Creation Date')
    	];
    }
}
