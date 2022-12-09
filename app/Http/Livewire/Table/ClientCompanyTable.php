<?php

namespace App\Http\Livewire\Table;

use App\Models\ClientCompanies;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class ClientCompanyTable extends LivewireDatatable
{
    public $model = ClientCompanies::class;

    public function builder()
    {
        return ClientCompanies::query();
    }

    public function columns()
    {
        return [
    		Column::name('client.name')->filterable()->label('Name'),
            NumberColumn::name('company_id')->label('Detail')->sortBy('company_id')->callback('company_id', function ($value) {
                return view('datatables::link', [
                    'href' => "/project?companyid=" . $value,
                    'slot' => 'View Project'
                ]);
            }),
    	];
    }
}
