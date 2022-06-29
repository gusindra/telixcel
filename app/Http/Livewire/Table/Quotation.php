<?php

namespace App\Http\Livewire\Table;


use App\Models\Quotation as ModelsQuotation;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class Quotation extends LivewireDatatable
{
    public $model = ModelsQuotation::class;

    public function columns()
    {
        return [
    		Column::name('title')->label('Title'),
    		Column::name('status')->label('Status')->filterable(['DRAFT', 'APPROVED', 'SUBMIT']),
    		Column::name('type')->label('Source')->filterable(),
    		DateColumn::name('date')->label('Date')->filterable(),
    		Column::name('price')->label('Total'),
    		NumberColumn::name('valid_day')->label('Duration')->filterable(),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/commercial/quotation/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
