<?php

namespace App\Http\Livewire\Template;

use App\Models\Template;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\BooleanColumn;

class TemplatesTable extends LivewireDatatable
{
    public $model = Template::class;

    public function columns()
    {
        return [
    		NumberColumn::name('uuid')->label('ID')->sortBy('id')->linkTo('template'),
    		Column::name('name')->label('Name'),
    		Column::name('description')->label('Description'),
    		Column::name('type')->label('Type'),
    		BooleanColumn::name('is_enabled')->label('Active')
    	];
    }
}
