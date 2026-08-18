<?php

namespace App\Http\Livewire\Table;

use App\Models\Company;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class Companies extends LivewireDatatable
{
    public $model = Company::class;

    public function builder()
    {
        return Company::query()->orderBy('created_at', 'desc');
    }

    public function columns()
    {
        return [
    		Column::name('id')->label('ID'),
    		Column::callback(['logo'], function ($value) {
                if($value){
                    return '<img src="https://telixcel.s3.ap-southeast-1.amazonaws.com/'.$value.'" />';
                }
                return '-';
            })->label('Logo'),
    		Column::name('name')->label('Name'),
    		Column::name('person_in_charge')->label('PIC'),
    		Column::name('address')->label('Address'),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback(['uuid', 'id'], function ($uuid, $id) {
                return view('datatables::link', [
                    'href' => '/company/'.($uuid ?: $id),
                    'slot' => 'View'
                ]);
            }),
            Column::callback(['id'], function ($id) {
                return view('tables.delete-action', [
                    'type' => 'company',
                    'id' => $id,
                    'label' => 'company',
                ]);
            })->label('Delete'),

    	];
    }
}
