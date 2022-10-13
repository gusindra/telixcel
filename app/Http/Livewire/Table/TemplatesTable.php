<?php

namespace App\Http\Livewire\Table;

use App\Models\Template;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\BooleanColumn;

class TemplatesTable extends LivewireDatatable
{
    public $model = Template::class;

    public function builder()
    {
        return Template::query()->with('teams')
            ->whereHas('teams', function ($query) {
                $query->where([
                    'teams.id' => auth()->user()->currentTeam->id
                ]);
            }); //->where('user_id', auth()->user()->currentTeam->user_id);
    }

    public function columns()
    {
        return [
    		NumberColumn::name('uuid')->label('ID')->sortBy('id')->callback('uuid', function ($value) {
                return view('datatables::link', [
                    'href' => "/template/" . $value,
                    'slot' => substr($value, 30)
                ]);
            }),
    		Column::name('name')->label('Name'),
    		Column::name('description')->label('Description'),
    		Column::callback(['type'], function ($type) {
                return view('template.label', ['type' => $type]);
            }),
    		BooleanColumn::name('is_enabled')->label('Active')
    	];
    }
}
