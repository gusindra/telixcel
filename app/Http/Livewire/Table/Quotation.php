<?php

namespace App\Http\Livewire\Table;


use App\Models\Quotation as ModelsQuotation;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class Quotation extends LivewireDatatable
{
    public $model = ModelsQuotation::class;

    public function columns()
    {
        return [
    		Column::name('title')->label('Title'),
    		Column::name('status')->label('Status'),
    		Column::name('type')->label('Source'),
    		Column::name('date')->label('Date'),
    		Column::name('price')->label('Total'),
    		Column::name('valid_day')->label('Duration'),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/commercial/quotation/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
