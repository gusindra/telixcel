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
    		Column::name('model')->callback('model, model_id, project.name, company.name, client.name', function ($m, $mi, $pn, $com, $cn) {
                if($m=='PROJECT'){
                    return $m.' : '.$pn;
                }elseif($m=='COMPANY'){
                    return $m.' : '.$com;
                }elseif($m=='CLIENT'){
                    return $m.' : '.$cn;
                }
                return $m;
            })->label('Source')->filterable(),
    		DateColumn::name('date')->label('Date')->filterable(),
    		NumberColumn::name('valid_day')->label('Duration (Day)')->filterable(),
    		Column::callback(['status'], function ($status) {
                return view('label.label', ['type' => $status]);
            })->label('Status')->filterable(['DRAFT', 'APPROVED', 'SUBMIT']),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/commercial/quotation/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
