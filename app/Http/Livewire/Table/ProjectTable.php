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

    public function builder()
    {
        return Project::query()->orderBy('updated_at', 'desc');
    }

    public function columns()
    {
        return [
    		Column::name('name')->label('Name'),
    		Column::name('customer_name')->label('Customer')->filterable(),
    		Column::name('type')->label('Type')->filterable(['Selling', 'SAAS', 'Referral']),
            Column::callback(['status'], function ($type) {
                return view('assistant.project.label', ['type' => $type]);
            })->label('Status')->filterable(['DRAFT', 'APPROVED', 'SUBMIT']),
            NumberColumn::name('link')->label('Link')->callback('id', function ($value) {
                return view('tables.link', [
                    'href' => "/project/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
