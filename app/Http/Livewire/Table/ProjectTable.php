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
    public $companyid;

    public function builder()
    {
        return Project::query()->where('party_b', "1")->orderBy('updated_at', 'desc');
    }

    public function columns()
    {
        return [
    		Column::name('name')->label('Name')->filterable(),
    		Column::name('customer_name')->label('Customer')->filterable(),
    		Column::callback(['type'], function ($type) {
                return view('label.type', ['type' => $type]);
            })->label('Type')->filterable(['Selling', 'SAAS', 'Referral']),
            Column::callback(['status'], function ($status) {
                return view('label.label', ['type' => $status]);
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
