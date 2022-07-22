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
    		Column::name('logo')->callback('logo, name', function ($value, $name) {
                if($value){
                    return '<img src="https://telixcel.s3.ap-southeast-1.amazonaws.com/'.$value.'" />';
                }
                return $name;
            })->label('Logo'),
    		Column::name('name')->label('Name'),
    		Column::name('person_in_charge')->label('PIC'),
    		Column::name('address')->label('Address'),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/company/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
